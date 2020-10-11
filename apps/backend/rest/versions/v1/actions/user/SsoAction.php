<?php

namespace rest\versions\v1\actions\user;

use rest\versions\v1\helpers\SsoHelper;
use tecnocen\oauth2server\Module;
use Yii;
use yii\authclient\clients\Google;
use yii\authclient\InvalidResponseException;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\rest\Action;
use yii\web\BadRequestHttpException;
use yii\web\RangeNotSatisfiableHttpException;
use yii\web\ServerErrorHttpException;



class SsoAction extends Action
{

    public $modelClass = 'unused';

    /**
     * @param $provider
     * @return array
     * @throws BadRequestHttpException
     * @throws RangeNotSatisfiableHttpException
     * @throws ServerErrorHttpException
     */
    public function run($provider)
    {
        switch ($provider) {
            case 'googleplus':
                return $this->ssoGooglePlus();
                break;
            default:
                throw new RangeNotSatisfiableHttpException();
        }
    }

    private function ssoGooglePlus()
    {
        $request = Yii::$app->getRequest();

        $domain = $request->getQueryParam('domain');

        $clientId = Yii::$app->params['GOOGLE_CLIENT_ID'];
        $clientSecret = Yii::$app->params['GOOGLE_CLIENT_SECRET'];
        $redirectUrl = Yii::$app->params['GOOGLE_SSO_REDIRECT_URL'];

        if (!$clientId || !$clientSecret || !$redirectUrl) {
            throw new ServerErrorHttpException('Misconfigured Google Client ID and Secret');
        }

        try {
            $client = Yii::createObject([
                'class' => Google::class,
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'validateAuthState' => false,
            ]);

            // In cases where the frontend is on a different domain from the backend
            $baseDomain = ($domain ? $domain : Url::base(true));


            // @TODO change the redirect URL on production
            $client->setReturnUrl($redirectUrl);
        } catch (InvalidConfigException $e) {
            Yii::error($e);
            throw new ServerErrorHttpException('The server SSO settings were not properly configured');
        }

        if (($code = Yii::$app->getRequest()
                              ->get('code')) !== null) {
            // We are in SSO callback
            try {
                $tokenStructure = $client->fetchAccessToken($code);
                if (!empty($tokenStructure)) {

                    $userIdInOurSystem = (new SsoHelper($client))->afterLogin();

                    if ($userIdInOurSystem) {
                        // Create an access token for this user and save it as a cookie (available for the frontend later)
                        /**
                         * @var $oauth Module
                         */
                        $oauth = Yii::$app->getModule('oauth2');
                        $oauth->initOauth2Server();

                        $tokenStructure = [
                            'access_token' => null,
                            'expires_in' => null,
                            'token_type' => null,
                            'scope' => null,
                        ];

                        return ArrayHelper::merge(
                            $tokenStructure,
                            $oauth
                                ->getServer()
                                ->createAccessToken(
                                    'website',
                                    $userIdInOurSystem,
                                    null,
                                    false)
                        );
                    } else {
                        throw new ServerErrorHttpException('SSO token successfully retrieved but our logic failed');
                    }
                } else {
                    Yii::error('Did not throw an error but also received an empty access token');
                }
            } catch (ServerErrorHttpException $e) {
                Yii::error($e);
            } catch (InvalidResponseException $e) {
                // Using an already used "code" GET parameter probably
                // Those can only be exchanged once
                Yii::error($e);
                throw new BadRequestHttpException('Invalid grant. Please, try repeating the SSO login');
            }
            throw new ServerErrorHttpException('SSO login failed due to server error');
        } else {
            // Create the URL where the user should be redirected to
            // There he will authorize the application to access his data
            $auth_url = $client->buildAuthUrl(['access_type' => 'offline']);
            Yii::info('Creating Google SSO auth URL: ' . $auth_url);
            return ['auth_url' => $auth_url];
        }
    }
}

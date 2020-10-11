<?php
/**
 * Created by PhpStorm.
 * User: dzhuneyt
 * Date: 14.02.19
 * Time: 17:47
 */

namespace rest\versions\v1\helpers;


use rest\versions\v1\helpers\google\GoogleSsoHelper;
use yii\authclient\ClientInterface;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\RangeNotSatisfiableHttpException;



class SsoHelper
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return integer The user ID inside our database
     * @throws Exception
     */
    public function afterLogin()
    {
        switch ($this->client->getId()) {
            case 'google':
                $helper = new GoogleSsoHelper();
                $helper->setProviderName($this->client->getId());
                $helper->setAttributes($this->client->getUserAttributes());

                $userIdInOurSystem = $helper->findUserId();

                if ($userIdInOurSystem !== false) {
                    // User logged in via SSO previously. Allow login
                    $helper->overwriteMetadata($userIdInOurSystem);
                    return $userIdInOurSystem;
                }

                // User didn't login through SSO yet
                // Step 1: try to match him to an existing user by email
                $userIdInOurSystem = $helper->findSuitableMatchByEmail();
                if ($userIdInOurSystem !== false) {
                    return $userIdInOurSystem;
                }
                // Step 2: if no match is found, create a user inside our system
                $userIdInOurSystem = $helper->registerUser();
                if ($userIdInOurSystem !== false) {
                    return $userIdInOurSystem;
                }

                throw new Exception('Failed to create SSO user in our system');
                break;
            default:
                throw new RangeNotSatisfiableHttpException('Not implemented');
        }
    }

    private function getEmail()
    {
        $attributes = $this->client->getUserAttributes();
        $email = ArrayHelper::getValue($attributes, 'email');

        if (!$email && isset($attributes['emails'])) {
            $emails = ArrayHelper::getValue($attributes, 'emails');
            if ($emails) {
                $email = reset($emails);
                $email = $email['value'] ? $email['value'] : null;
            }
        }

        return $email;
    }
}

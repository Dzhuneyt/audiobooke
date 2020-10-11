<?php
namespace console\controllers;


use tecnocen\oauth2server\models\OauthClients;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;



class OauthClientController extends Controller
{

    public function actionCreate($clientId)
    {
        $model = new OauthClients();
        $model->client_id = $clientId;
        $model->client_secret = Yii::$app->security->generateRandomString(32);
        $model->redirect_uri = 'http://example.com';
        $model->grant_types = 'password';
        if (!$model->save()) {
            echo PHP_EOL
                . 'Can not create Oauth Client due to validation errors'
                . PHP_EOL;
            var_dump($model->getErrors());
            return ExitCode::UNSPECIFIED_ERROR;
        }

        echo 'Oauth client created successfully' . PHP_EOL;
        foreach ($model->getAttributes() as $name => $value) {
            echo $name . ':' . $value . PHP_EOL;
        }
        return ExitCode::OK;
    }

    public function actionDelete($clientId)
    {
        $rowsDeleted = OauthClients::deleteAll(['client_id' => $clientId]);

        if ($rowsDeleted > 0) {
            echo 'Deleted rows: ' . $rowsDeleted;
            return ExitCode::OK;
        } else {
            echo 'Can not find client with this ID';
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}

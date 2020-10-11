<?php

use tecnocen\oauth2server\models\OauthClients;
use yii\db\Migration;



/**
 * Class m190301_151409_CREATE_FIRST_OAUTH_CLIENT
 */
class m190301_151409_CREATE_FIRST_OAUTH_CLIENT extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $model = new OauthClients();
        $model->client_id = 'website';
        $model->client_secret = Yii::$app->security->generateRandomString(32);
        $model->redirect_uri = 'http://example.com';
        $model->grant_types = 'password';
        if (!$model->save()) {
            echo PHP_EOL
                . 'Can not create Oauth Client due to validation errors'
                . PHP_EOL;
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        OauthClients::deleteAll(['client_id' => 'website']);
    }

}

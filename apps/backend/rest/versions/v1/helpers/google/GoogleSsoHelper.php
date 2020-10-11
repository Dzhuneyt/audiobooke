<?php

namespace rest\versions\v1\helpers\google;


use common\models\User;
use common\models\UserSsoProvider;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;



class GoogleSsoHelper
{
    private $userAttributes = [];
    private $providerName;

    public function setAttributes(array $userAttributes)
    {
        $this->userAttributes = $userAttributes;
    }

    public function getId()
    {
        return ArrayHelper::getValue($this->userAttributes, 'id');
    }

    public function getEmail()
    {
        if ($this->userAttributes['verified_email']) {
            $email = ArrayHelper::getValue($this->userAttributes, 'email');
        } else {
            $email = false;
        }

        if (!$email && isset($this->userAttributes['emails'])) {
            $emails = ArrayHelper::getValue($this->userAttributes, 'emails');
            if ($emails) {
                $email = reset($emails);
                $email = $email['value'] ? $email['value'] : null;
            }
        }
        return $email;
    }

    public function findUserId()
    {
        $model = UserSsoProvider::findOne([
            'source' => 'google',
            'source_id' => $this->getId(),
        ]);
        if (!$model) {
            return false;
        }

        $model = User::findOne($model->user_id);
        if (!$model) {
            return false;
        }

        return $model->id;
    }

    public function findSuitableMatchByEmail()
    {
        Yii::info('Finding SSS user by email for ' . $this->getEmail());

        $exists = User::find()->where([
            'email' => $this->getEmail(),
        ])->one();
        if (!$exists) {
            Yii::info('Match not found');
            return false;
        }

        Yii::info('Found ID: ' . $exists->id);
        return $exists->id;
    }

    /**
     * @throws \yii\db\Exception
     */
    public function registerUser()
    {
        Yii::info('Registering user with email ' . $this->getEmail());

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Register the user to the platform
            $user = new User();
            $user->username = $this->getEmail();
            $user->email = $this->getEmail();
            $user->password_hash = Yii::$app->security->generateRandomString(12);
            $user->auth_key = Yii::$app->security->generateRandomString(32);
            $user->password_reset_token = Yii::$app->security->generateRandomString(32);

            if (!$user->save()) {
                Yii::error($user->getErrors());
                throw new Exception('Can not register user - internal error');
            }

            // Connect the user to his SSO auth ID (for future SSO logins)
            $auth = new UserSsoProvider();
            $auth->user_id = $user->id;
            $auth->source = $this->providerName;
            $auth->source_id = $this->getId();
            if (!$auth->save()) {
                Yii::error($auth->getErrors());
                throw new Exception('Can not register user - SSO linking failed');
            }

            $transaction->commit();

            return $user->id;
        } catch (\Exception $e) {
            if ($transaction->getIsActive()) {
                $transaction->rollBack();
            }
            throw $e;
        }
    }

    public function setProviderName(string $providerName)
    {
        $this->providerName = $providerName;
    }

    public function overwriteMetadata($idUser)
    {
        try {
            $user = User::findOne($idUser);
            if (!$user) {
                throw new Exception('Can not find user whose metadata to overwrite');
            }

            $user->email = $this->getEmail();
            if (!$user->save()) {
                throw new \yii\db\Exception('Can not overwrite user metadata from SSO provider');
            }
        } catch (\Exception $e) {
            Yii::error($e);
        }
        return true;
    }
}

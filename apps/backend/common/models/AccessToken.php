<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;



/**
 * This is the model class for table "access_token".
 *
 * @property int $id
 * @property int $id_user
 * @property string $access_token
 * @property string $created_at
 * @property string $expires
 *
 * @property User $user
 */
class AccessToken extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access_token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user'], 'required'],
            [['id_user'], 'integer'],
            [['created_at', 'expires'], 'safe'],
            [['access_token'], 'string', 'max' => 255],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_user' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_user' => 'Id User',
            'access_token' => 'Access Token',
            'created_at' => 'Created At',
            'expires' => 'Expires',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }
}

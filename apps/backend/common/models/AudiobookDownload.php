<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;



/**
 * This is the model class for table "audiobook_download".
 *
 * @property int $id
 * @property int $id_audiobook
 * @property int $id_user
 * @property string $created_at
 *
 * @property Audiobook $audiobook
 * @property User $user
 */
class AudiobookDownload extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audiobook_download';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_audiobook', 'id_user'], 'required'],
            [['id_audiobook', 'id_user'], 'integer'],
            [['created_at'], 'safe'],
            [['id_audiobook'], 'exist', 'skipOnError' => true, 'targetClass' => Audiobook::className(), 'targetAttribute' => ['id_audiobook' => 'id']],
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
            'id_audiobook' => 'Id Audiobook',
            'id_user' => 'Id User',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAudiobook()
    {
        return $this->hasOne(Audiobook::className(), ['id' => 'id_audiobook']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }
}

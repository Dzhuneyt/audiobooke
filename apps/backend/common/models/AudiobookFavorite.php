<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;



/**
 * This is the model class for table "audiobook_favorite".
 *
 * @property int $id
 * @property int $id_book
 * @property int $id_user
 * @property string $created_at
 *
 * @property Audiobook $book
 * @property User $user
 */
class AudiobookFavorite extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audiobook_favorite';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_book', 'id_user'], 'required'],
            [['id_book', 'id_user'], 'integer'],
            [['created_at'], 'safe'],
            [['id_book'], 'exist', 'skipOnError' => true, 'targetClass' => Audiobook::className(), 'targetAttribute' => ['id_book' => 'id']],
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
            'id_book' => 'Id Book',
            'id_user' => 'Id User',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Audiobook::className(), ['id' => 'id_book']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }
}

<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;



/**
 * This is the model class for table "audiobook_rating".
 *
 * @property int $id_book
 * @property int $id_user
 * @property int $rating
 * @property int $created_at
 * @property int $updated_at
 * @property string $last_ip
 *
 * @property Audiobook $book
 * @property User $user
 */
class AudiobookRating extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audiobook_rating';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_book', 'id_user', 'rating', 'created_at', 'updated_at'], 'required'],
            [['id_book', 'id_user', 'rating', 'created_at', 'updated_at'], 'integer'],
            [['last_ip'], 'string', 'max' => 255],
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
            'id_book' => 'Id Book',
            'id_user' => 'Id User',
            'rating' => 'Rating',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'last_ip' => 'Last Ip',
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

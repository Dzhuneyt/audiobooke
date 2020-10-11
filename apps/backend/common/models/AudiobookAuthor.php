<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;



/**
 * This is the model class for table "audiobook_author".
 *
 * @property int $id_book
 * @property int $id_author
 *
 * @property Audiobook $book
 * @property Author $author
 */
class AudiobookAuthor extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audiobook_author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_book', 'id_author'], 'required'],
            [['id_book', 'id_author'], 'integer'],
            [['id_book', 'id_author'], 'unique', 'targetAttribute' => ['id_book', 'id_author']],
            [
                ['id_book'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Audiobook::className(),
                'targetAttribute' => ['id_book' => 'id']
            ],
            [
                ['id_author'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Author::className(),
                'targetAttribute' => ['id_author' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_book' => 'Id Book',
            'id_author' => 'Id Author',
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
    public function getAuthor()
    {
        return $this->hasOne(Author::className(), ['id' => 'id_author']);
    }
}

<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;



/**
 * This is the model class for table "author".
 *
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property int $dob Date of birth
 * @property int $dod Date of death
 *
 * @property AudiobookAuthor[] $audiobookAuthors
 * @property Audiobook[] $books
 */
class Author extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstname'], 'required'],
            [['dob', 'dod'], 'integer'],
            [['firstname', 'lastname'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'dob' => 'Dob',
            'dod' => 'Dod',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAudiobookAuthors()
    {
        return $this->hasMany(AudiobookAuthor::className(), ['id_author' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Audiobook::className(), ['id' => 'id_book'])->viaTable('audiobook_author', ['id_author' => 'id']);
    }
}

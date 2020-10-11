<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;



/**
 * This is the model class for table "audiobook_cover".
 *
 * @property int $id
 * @property int $id_book
 * @property string $url
 *
 * @property Audiobook $book
 */
class AudiobookCover extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audiobook_cover';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_book'], 'integer'],
            [['url'], 'string', 'max' => 2000],
            [
                ['id_book'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Audiobook::className(),
                'targetAttribute' => ['id_book' => 'id']
            ],
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
            'url' => 'Url',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Audiobook::className(), ['id' => 'id_book']);
    }
}

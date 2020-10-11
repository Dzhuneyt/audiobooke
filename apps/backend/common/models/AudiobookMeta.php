<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;



/**
 * This is the model class for table "audiobook_meta".
 *
 * @property int $id_book
 * @property string $name
 * @property string $value
 *
 * @property Audiobook $book
 */
class AudiobookMeta extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audiobook_meta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_book', 'name'], 'required'],
            [['id_book'], 'integer'],
            [['value'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['id_book', 'name'], 'unique', 'targetAttribute' => ['id_book', 'name']],
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
            'id_book' => 'Id Book',
            'name' => 'Name',
            'value' => 'Value',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Audiobook::className(), ['id' => 'id_book']);
    }

    public function getMetaValue($idbook, $metaName)
    {
        return AudiobookMeta::find()
                            ->select('value')
                            ->where([
                                'id_book' => $idbook,
                                'name' => $metaName,
                            ])
                            ->scalar();
    }
}

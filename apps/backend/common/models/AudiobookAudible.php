<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;



/**
 * This is the model class for table "audiobook_audible".
 *
 * @property int $id_book
 * @property string $isbn
 * @property string $audible_url
 * @property string $read_by
 * @property int $abridged
 * @property string $date_published
 * @property double $rating
 * @property int $rating_count
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Audiobook $book
 *
 * @method touch($attribute)
 */
class AudiobookAudible extends ActiveRecord
{

    const SKIP_IF_NEWER_THAN_N_HOURS = 24;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audiobook_audible';
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => TimestampBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
            ],
            // if you're using datetime instead of UNIX timestamp:
            // 'value' => new Expression('NOW()'),
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_book', 'isbn'], 'required'],
            [['id_book', 'abridged', 'rating_count', 'created_at', 'updated_at'], 'integer'],
            [['date_published'], 'safe'],
            [['rating'], 'number'],
            [['isbn'], 'string', 'max' => 10],
            [['audible_url'], 'string', 'max' => 2000],
            [['read_by'], 'string', 'max' => 255],
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
            'isbn' => 'Isbn',
            'audible_url' => 'Audible Url',
            'read_by' => 'Read By',
            'abridged' => 'Abridged',
            'date_published' => 'Date Published',
            'rating' => 'Rating',
            'rating_count' => 'Rating Count',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Audiobook::className(), ['id' => 'id_book']);
    }

    public function needsMetadataRefresh()
    {
        $currentTime = time();
        $bookUpdatedAt = $this->updated_at;

        $nextScheduledUpdate =
            $bookUpdatedAt + (self::SKIP_IF_NEWER_THAN_N_HOURS * 60 * 60);
        return $nextScheduledUpdate < $currentTime;

    }
}

<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\HtmlPurifier;



/**
 * This is the model class for table "{{%audiobook}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $language
 * @property integer $copyright_year
 * @property integer $num_sections
 * @property string $url_zip_file
 * @property integer $totaltimesecs
 * @property string $type
 *
 * @property AudiobookMeta[] $audiobookMetas
 */
class Audiobook extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%audiobook}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['copyright_year', 'num_sections', 'totaltimesecs'], 'integer'],
            [['title'], 'string', 'max' => 500],
            [['language'], 'string', 'max' => 50],
            [['url_zip_file'], 'string', 'max' => 255],
            [['type'], 'in', 'range' => ['librivox', 'audible']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'language' => Yii::t('app', 'Language'),
            'copyright_year' => Yii::t('app', 'Copyright Year'),
            'num_sections' => Yii::t('app', 'Num Sections'),
            'url_zip_file' => Yii::t('app', 'Url Zip File'),
            'totaltimesecs' => Yii::t('app', 'Totaltimesecs'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAudiobookMetas()
    {
        return $this->hasMany(AudiobookMeta::className(), ['id_book' => 'id']);
    }

    public static function getBookByMeta($metaName, $metaValue)
    {
        $meta = AudiobookMeta::findOne(['name' => $metaName, 'value' => $metaValue]);
        if ($meta) {
            return Audiobook::findOne(['id' => $meta->id_book]);
        }

        return null;
    }

    public static function setBookMeta($bookId, $metaName, $metaValue)
    {
        $meta = AudiobookMeta::findOne(['id_book' => $bookId, 'name' => $metaName]);
        if (!$meta) {
//            Yii::error('Creating new meta');
//            Yii::error([$bookId, $metaName, $metaValue]);
            $meta = new AudiobookMeta();
            $meta->id_book = $bookId;
            $meta->name = $metaName;
        }
        $meta->value = $metaValue;
        if ($meta->save()) {
            Yii::info('Saved book meta for book ' . $bookId . '. Meta: ' . $metaName . ', ', $metaValue);

            return true;
        } else {
            return false;
        }
    }

    public static function totalCount()
    {
        return (int)(new Query())->from(self::tableName())
                                 ->count('id');
    }

    public static function getRating($id)
    {
        $rating = (new Query())->select('AVG(rating)')
                               ->from('audiobook_rating')
                               ->where(['id_book' => $id])
                               ->scalar();

        return floatval($rating);
    }

    public function isFavoritedByUser($idAudiobok, $idUser)
    {
        return (new Query())
            ->from(AudiobookFavorite::tableName())
            ->where([
                'id_book' => $idAudiobok,
                'id_user' => $idUser,
            ])
            ->exists();
    }

    static public function formatSingleAudiobook($row)
    {
        $paramsToBeCast = [
            'id',
            'year',
            'num_sections',
            'total_seconds'
        ];

        foreach ($paramsToBeCast as $attr) {
            if (!isset($row[$attr])) {
                continue;
            }

            $row[$attr] = intval($row[$attr]);
        }
        if (isset($row['description'])) {
            $row['description'] = HtmlPurifier::process($row['description'], [
                'HTML.Allowed' => ''
            ]);
        }

        switch ($row['type']) {
            case 'audible':
                break;
            default:
                unset($row['audible_url']);
                break;
        }

        if (!$row['type']) {
            $row['type'] = 'librivox';
        }

        unset($row['copyright_year'], $row['url_zip_file'], $row['totaltimesecs']);

        return $row;
    }
}

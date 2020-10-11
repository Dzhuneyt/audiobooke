<?php
/**
 * Created by PhpStorm.
 * User: dzhuneyt
 * Date: 10.02.19
 * Time: 22:29
 */

namespace rest\versions\v1\actions\audiobook;


use common\models\Audiobook;
use common\models\AudiobookAudible;
use common\models\AudiobookAuthor;
use common\models\AudiobookCover;
use common\models\AudiobookMeta;
use common\models\Author;
use Yii;
use yii\db\Query;



class ViewAction extends \yii\rest\ViewAction
{
    /**
     * @var AudiobookMeta
     */
    private $meta;

    /**
     * @var Audiobook
     */
    private $audiobook;

    public function init()
    {
        parent::init();
        $this->meta = Yii::$container->get(AudiobookMeta::class);

    }

    public function run($id)
    {
        $this->audiobook = Audiobook::findOne($id);

        /** @var Audiobook $model */
        $model = (new Query())
            ->from(['a' => Audiobook::tableName()])
            ->where(['a.id' => $id])
            ->select([
                'a.id',
                'a.title',
                'a.description',
                'a.language',
                'a.type',
                'year'          => 'a.copyright_year',
                'total_seconds' => 'a.totaltimesecs',
                'author_name' => 'TRIM(CONCAT(COALESCE(MIN(author.firstname),""), " ", COALESCE(MIN(author.lastname),"")))',
                'cover_url'     => 'MIN(cover.url)',
                'audible_url' => 'MIN(audible.audible_url)',
            ])
            ->leftJoin(['aaa' => AudiobookAuthor::tableName()], 'a.id=aaa.id_book')
            ->leftJoin(['audible' => AudiobookAudible::tableName()], 'a.id=audible.id_book')
            ->leftJoin(['author' => Author::tableName()], 'aaa.id_author=author.id')
            ->leftJoin(['cover' => AudiobookCover::tableName()], 'a.id=cover.id_book')
            ->groupBy('a.id')
            ->one();
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $book = Audiobook::formatSingleAudiobook($model);

        if (!Yii::$app->user->getIsGuest()) {
            // @TODO make this work
            $book['is_favorited'] = $this
                ->audiobook
                ->isFavoritedByUser(
                    $this->audiobook->id,
                    Yii::$app->user->id
                );
        }

        return $book;
    }
}

<?php

namespace rest\versions\v1\actions\audiobook;


use Aws\Batch\BatchClient;
use Aws\S3\S3Client;
use Aws\Sqs\SqsClient;
use common\models\Audiobook;
use common\models\AudiobookAuthor;
use common\models\AudiobookCover;
use common\models\Author;
use console\jobs\SearchJob;
use rest\common\SearchComponent;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQueryInterface;
use yii\db\Exception;
use yii\db\Query;
use yii\di\NotInstantiableException;



class IndexAction extends \rest\common\actions\IndexAction
{

    /**
     * @var SearchComponent
     */
    private $search = null;

    /**
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function init()
    {
        parent::init();

//        Yii::$app->mailer->compose()
//                         ->setFrom('from@domain.com')
//                         ->setTo('to@domain.com')
//                         ->setSubject('Message subject')
//                         ->setTextBody('Plain text content')
//                         ->setHtmlBody('<b>HTML content</b>')
//                         ->send();

        $this->search = Yii::$container->get(SearchComponent::class);

        $this->rowFormatter = function ($row) {
            $cacheKey = 'single_row_' . $row['id'];
            $row = Yii::$app->cache->getOrSet(
                $cacheKey,
                function ($cache) use ($row) {
                    return Audiobook::formatSingleAudiobook($row);
                },
                60
            );

            return $row;
        };

        $this->dataFilter = [
            'class' => 'yii\data\ActiveDataFilter',
            'searchModel' => function () {
                return (new DynamicModel([
                    'id' => null,
                    'title' => null,
                    'description' => null,
                    'copyright_year' => null
                ]))
                    ->addRule('id', 'integer')
                    ->addRule('title', 'trim')
                    ->addRule('title', 'string')
                    ->addRule('copyright_year', 'number');
            },
        ];
    }

    /**
     * @return ActiveDataProvider
     * @throws Exception
     */
    public function run()
    {
        $search = Yii::$app->request->getQueryParam('search');
        Yii::info("Searched:  " . $search);

        if (!$search) {
            return parent::run();
        }

        // Save this search for future use and date processing
        $this->search->save($search);

        Yii::$app->queue->push(new SearchJob([
            'keyword' => $search,
        ]));
        return parent::run();
    }


    /**
     * @return ActiveQueryInterface|Query
     */
    protected function getMainQuery()
    {
        $query = (new Query())
            ->select([
                'a.*',
                'total_seconds' => 'a.totaltimesecs',
                'author_name' => 'TRIM(CONCAT(COALESCE(MIN(author.firstname),""), " ", COALESCE(MIN(author.lastname),"")))',
                'cover_url' => 'MIN(cover.url)',
                'year' => 'copyright_year',
            ])
            ->from(['a' => Audiobook::tableName()])
            ->leftJoin(['aa' => AudiobookAuthor::tableName()], 'a.id=aa.id_book')
            ->leftJoin(['author' => Author::tableName()], 'aa.id_author=author.id')
            ->leftJoin(['cover' => AudiobookCover::tableName()], 'a.id=cover.id_book')
            ->groupBy('a.id')
            ->orderBy('a.id DESC');

        $search = Yii::$app->request->getQueryParam('search');
        if ($search) {
            $query->andWhere(
                'a.title LIKE :search OR author.firstname LIKE :search OR author.lastname LIKE :search OR CONCAT_WS(" ", author.firstname, author.lastname) LIKE :search',
                [':search' => '%' . $search . '%']);
        }

        return $query;
    }

    protected function getTotalCount()
    {
        return $this->getMainQuery()
                    ->count('id');
    }

}

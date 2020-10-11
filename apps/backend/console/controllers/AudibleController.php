<?php

namespace console\controllers;


use common\models\Audiobook;
use common\models\AudiobookAudible;
use console\jobs\AudibleIsbnJob;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Query;
use yii\queue\Queue;



class AudibleController extends Controller
{

    public function actionRefresh()
    {
        $isbns = (new Query())
            ->select('aa.isbn')
            ->from(['a' => Audiobook::tableName()])
            ->innerJoin(['aa' => AudiobookAudible::tableName()], 'a.id=aa.id_book')
            ->column();

        if (empty($isbns)) {
            $this->stderr('No audible audiobooks present');
            return ExitCode::DATAERR;
        }

        $this->stdout('Refreshing ' . count($isbns) . ' elements' . PHP_EOL);

        /** @var Queue $queue */
        $queue = Yii::$app->queue;

        $exponentialBackoffSeconds = 5;
        foreach ($isbns as $isbn) {
            echo 'Scheduling job for ISBN ' . $isbn . ' ' . $exponentialBackoffSeconds . ' seconds from now' . PHP_EOL;

            $queue
                ->delay($exponentialBackoffSeconds)
                ->push(new AudibleIsbnJob([
                    'isbn' => $isbn,
                ]));

            // Give some interval between each job
            $exponentialBackoffSeconds += rand(1, 9);
        }
        return ExitCode::OK;
    }

}

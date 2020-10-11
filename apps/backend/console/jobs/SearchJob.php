<?php

namespace console\jobs;


use Aws\Sqs\SqsClient;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\queue\JobInterface;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;



class SearchJob extends BaseObject implements JobInterface, RetryableJobInterface
{
    use ScraperJob;

    public $keyword;

    /**
     * @param Queue $queue which pushed and is handling the job
     *
     * @return void|mixed result of the job execution
     * @throws \Exception
     */
    public function execute($queue)
    {
        \Yii::error('Searching ');

        if (getenv('QUEUE_URL_FOR_SEARCHES') && getenv('QUEUE_REGION')) {
            Yii::error('Queue params present');
            $cl = new SqsClient([
                'region' => getenv('QUEUE_REGION'),
                'version' => "2012-11-05",
            ]);

            $cl->sendMessage([
                'QueueUrl' => getenv('QUEUE_URL_FOR_SEARCHES'),
                'MessageBody' => urlencode($this->keyword),
            ]);
        }
//        $response = $this->callScraper('audible/search/' . urlencode($this->keyword));
//        $isbns = $response['isbn_list'];
//
//        if ($isbns == null) {
//            throw new Exception('Failed to access scraper for search keyword ' . $this->keyword);
//        }

//        foreach ($isbns as $isbn) {
//            echo 'Scheduling job for ISBN ' . $isbn . PHP_EOL;
//
//            Yii::$app->queue->push(new AudibleIsbnJob([
//                'isbn' => $isbn,
//            ]));
//        }
    }

    /**
     * @return int time to reserve in seconds
     */
    public function getTtr()
    {
        return 60; // timeout in seconds
    }

    /**
     * @param int $attempt number
     * @param \Exception|Throwable $error from last execute of the job
     * @return bool
     */
    public function canRetry($attempt, $error)
    {
        return $attempt <= 3;
    }
}

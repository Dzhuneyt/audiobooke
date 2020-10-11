<?php

namespace console\jobs;


use common\models\Audiobook;
use common\models\AudiobookAudible;
use common\models\AudiobookAuthor;
use common\models\AudiobookCover;
use common\models\Author;
use DateInterval;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\db\Query;
use yii\queue\JobInterface;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;



class AudibleIsbnJob extends BaseObject implements JobInterface, RetryableJobInterface
{
    use ScraperJob;

    public $isbn;

    private $jsonResponse;
    private $audibleLink;
    private $isNewRecord = false;

    /**
     * @param Queue $queue which pushed and is handling the job
     *
     * @return void|mixed result of the job execution
     * @throws \Exception
     */
    public function execute($queue)
    {
        $this->log('Importing ISBN ' . $this->isbn);

        $oldModel = AudiobookAudible::findOne(['isbn' => $this->isbn]);

        if ($oldModel && !$oldModel->needsMetadataRefresh()) {
            /**
             * @var Audiobook $book
             */
            $book = $oldModel
                ->getBook()
                ->one();
            if ($book) {
                $this->log('"' . $book->title . '" is new enough');
            }
            $this->log('Last update: ' . date('Y-m-d H:i:s', $oldModel->updated_at));
        } else {
            $db = Yii::$app->getDb();
            $transaction = $db->beginTransaction();

            try {
                // @TODO replace hardcoded domain with a dynamic dev/production value
                $response = $this->callScraper('audible/' . $this->isbn);

                if (!isset($response['ld_json'])) {
                    throw new Exception('Scraper did not return proper JSON response');
                }

                if (isset($response['extra_isbns'])) {
                    $this->scheduleDeferredIsbnJobs($response['extra_isbns']);
                }

                $this->jsonResponse = $response['ld_json'];
                $this->audibleLink = $response['link'];
                $title = $this->jsonResponse['name'];

                $this->log('Processing audiobook ' . $title);

                $baseAudiobook = $this->createOrUpdateBaseAudiobook();

                $authorNames = $this->getAuthors();
                $this->createOrUpdateAuthorsInOurSystem($authorNames);
                $this->attachBookToAuthors($baseAudiobook, $authorNames);
                $this->fillAudibleMetadata($baseAudiobook);
                $this->addCoverImage($baseAudiobook);

                $this->log("Audiobook scraped: " . $title);

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();

                $this->log('Failed to import audiobook for generic reasons');

                $this->log($this->jsonResponse);

                throw $e;

                // @TODO Use the mailer component to notify site owner of fatal errors
            }
        }


    }

    private function getAuthors()
    {
        $response = [];
        foreach ($this->jsonResponse['author'] as $author) {
            $firstname = null;
            $lastname = null;

            if (count(explode(' ', $author['name'])) === 2) {
                $firstname = explode(' ', $author['name'])[0];
                $lastname = explode(' ', $author['name'])[1];
            } else {
                // This guy probably has 3-4 names. We don't know which part
                // is firstname and which part is lastname, so assume everything
                // to be "firstname". This can be improved in the future
                $firstname = $author['name'];
                $lastname = null;
            }

            $authorMeta = [
                'firstname' => $firstname,
                'lastname' => $lastname
            ];
            $response[] = $authorMeta;
        };

        return $response;
    }

    /**
     * @param array $authorMetas
     *
     * @throws Exception
     */
    private function createOrUpdateAuthorsInOurSystem(array $authorMetas)
    {
        foreach ($authorMetas as $authorMeta) {
            $firstname = $authorMeta['firstname'];
            $lastname = $authorMeta['lastname'];

            $found = Author::find()
                           ->where([
                               'firstname' => $firstname,
                               'lastname' => $lastname,
                           ])
                           ->one();

            if ($found) {
                Yii::debug("Author already exists: {$firstname} {$lastname}. Skipping");
                continue;
            }

            // Create author in our DB
            $authorModel = new Author();
            $authorModel->firstname = $firstname;
            $authorModel->lastname = $lastname;
            if (!$authorModel->save()) {
                $this->log($authorModel->getErrors());
                throw new Exception("Updating author {$firstname} {$lastname} failed");
            } else {

                $this->log("Author created: {$firstname} {$lastname}");
            }
        }
    }

    private function log($str)
    {
        if (is_string($str)) {
            $str = '[ISBN:' . $this->isbn . '] ' . $str;
            echo $str . PHP_EOL;
        } else {
            echo '[ISBN:' . $this->isbn . '] ' . print_r($str, true);
            echo PHP_EOL;
        }
        Yii::info($str);
    }

    /**
     * @param $ISO8601
     *
     * @return float|int
     * @throws \Exception
     */
    private function ISO8601ToSeconds($ISO8601)
    {
        $interval = new DateInterval($ISO8601);

        return ($interval->d * 24 * 60 * 60) +
            ($interval->h * 60 * 60) +
            ($interval->i * 60) +
            $interval->s;
    }

    /**
     * @return Audiobook
     * @throws Exception
     * @throws \Exception
     */
    private function createOrUpdateBaseAudiobook()
    {
        $isbn = $this->isbn;

        $oldId = (new Query())
            ->select('a.id')
            ->from(['a' => 'audiobook'])
            ->innerJoin(['aa' => 'audiobook_audible'], 'a.id=aa.id_book')
            ->andWhere(['aa.isbn' => $isbn])
            ->scalar();

        if (!$oldId) {

            $audiobook = Audiobook::find()
                                  ->where([
                                      'title' => $this->jsonResponse['name'],
                                  ])
                                  ->one();
            if ($audiobook) {
                $this->log('The book was found but is not currently attached to an Audible entry. Attaching now...');
            } else {
                $this->log("No base audiobook found. Creating new...");
                $audiobook = new Audiobook();
                $this->isNewRecord = true;
            }
        } else {
            $this->log("Old base audiobook found. ID is {$oldId}");
            $audiobook = Audiobook::findOne($oldId);
        }

        $audiobook->title = $this->jsonResponse['name'];
        $audiobook->description = $this->jsonResponse['description'];
        $audiobook->language = ucfirst($this->jsonResponse['inLanguage']);
        $audiobook->copyright_year = mb_substr($this->jsonResponse['datePublished'], 0, 4);
        if (isset($this->jsonResponse['duration'])) {
            $audiobook->totaltimesecs = $this->ISO8601ToSeconds($this->jsonResponse['duration']);
        }
        $audiobook->type = 'audible';
        if (!$audiobook->save()) {
            $this->log($audiobook->getErrors());
            throw new Exception('Can not save base audiobook');
        }

        return $audiobook;
    }

    /**
     * @param Audiobook $baseAudiobook
     * @param array $authorMetas
     *
     * @throws Exception
     */
    private function attachBookToAuthors(Audiobook $baseAudiobook, array $authorMetas)
    {
        foreach ($authorMetas as $authorMeta) {
            $first = $authorMeta['firstname'];
            $last = $authorMeta['lastname'];

            $author = Author::find()
                            ->where([
                                'firstname' => $first,
                                'lastname' => $last,
                            ])
                            ->one();

            if (!$author) {
                throw new Exception(
                    "Could not attach base audiobook " . $baseAudiobook->title . " " .
                    "to author {$first} {$last} " .
                    "because the author was not found in our system"
                );
            }

            // Check if already attached
            $alreadyConnected = AudiobookAuthor::find()
                                               ->where([
                                                   'id_book' => $baseAudiobook->id,
                                                   'id_author' => $author->id,
                                               ])
                                               ->exists();
            if ($alreadyConnected) {
                continue;
            }

            // Connect the book to this author
            $aa = new AudiobookAuthor([
                'id_book' => $baseAudiobook->id,
                'id_author' => $author->id,
            ]);
            if (!$aa->save()) {
                $this->log($aa->getErrors());
                throw new Exception(
                    "Could not attach base audiobook {$baseAudiobook['name']} " .
                    "to author {$first} {$last} " .
                    "due to model validation"
                );
            }
        }
    }

    /**
     * @param Audiobook $baseAudiobook
     *
     * @throws Exception
     */
    private function fillAudibleMetadata(Audiobook $baseAudiobook)
    {

        // Search for an already "attached" Audible model
        $model = AudiobookAudible::find()
                                 ->where(['id_book' => $baseAudiobook->id])
                                 ->one();

        if (!$model) {
            // Fallback. Search for a "stranded" or "detached" model
            $model = AudiobookAudible::find()
                                     ->where(['isbn' => $this->isbn])
                                     ->one();
        }

        if (!$model) {
            // As last resort, create an audible metadata row
            $model = new AudiobookAudible();
        }

        $model->isbn = $this->isbn;
        $model->id_book = $baseAudiobook->id;
        $model->audible_url = $this->audibleLink;
        if (isset($this->jsonResponse['readBy'])) {
            $model->read_by = $this->jsonResponse['readBy'][0]['name'];
        }
        if (isset($this->jsonResponse['abridged'])) {
            $model->abridged = $this->jsonResponse['abridged'] ? 1 : 0;
        }
        $model->date_published = $this->jsonResponse['datePublished'];

        if (isset($this->jsonResponse['aggregateRating'])) {
            $model->rating = floatval($this->jsonResponse['aggregateRating']['ratingValue']);
            $model->rating_count = intval($this->jsonResponse['aggregateRating']['ratingCount']);
        }

        // Workaround for a bug with TimestampBehavior
        // @TODO investigate why it doesn't update timestamp otherwise
        if (!$model->isNewRecord) {
            $model->touch('updated_at');
        }

        if (!$model->save()) {
            $this->log($model->getErrors());
            throw new Exception("Can not fill Audible metadata due to model validation errors");
        }
    }

    /**
     * @param Audiobook $baseAudiobook
     *
     * @throws Exception
     */
    private function addCoverImage(Audiobook $baseAudiobook)
    {
        $model = AudiobookCover::findOne(['id_book' => $baseAudiobook->id]);

        if (!$model) {
            $model = new AudiobookCover([
                'id_book' => $baseAudiobook->id
            ]);
        }

        $model->url = $this->jsonResponse['image'];
        if (!$model->save()) {
            $this->log($model->getErrors());
            throw new Exception("Can not attach cover to audiobook");
        }
    }

    /**
     * @return int time to reserve in seconds
     */
    public function getTtr()
    {
        return 30; // Job killed after seconds
    }

    /**
     * @param int $attempt number
     * @param \Exception|Throwable $error from last execute of the job
     *
     * @return bool
     */
    public function canRetry($attempt, $error)
    {
        if ($attempt > 5) {
            return false;
        }

        return true;
    }

    private function scheduleDeferredIsbnJobs($extraIsbnsArray)
    {
        foreach ($extraIsbnsArray as $isbn) {
            $this->log('Scheduling deferred job for ISBN ' . $isbn);

            /**
             * @var $queue Queue
             */
            $queue = Yii::$app->queue;
            // Schedule the deferred job somewhere in the next 2 days
            $queue->delay(rand(60, 3600 * 24 * 2))
                  ->push(new AudibleIsbnJob([
                      'isbn' => $isbn,
                  ]));
        }
    }
}

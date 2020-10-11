<?php

namespace tests\functional\v1\actions\audiobook;

use fixtures\AudiobookFixture;
use tests\functional\FunctionalTestCase;
use Throwable;
use yii\base\Exception;
use yii\db\StaleObjectException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;



class IndexActionTest extends FunctionalTestCase
{

    /**
     * @return array
     * @TODO Reduce fixtures size for better testing speeds
     */
    public function fixtures()
    {
        return [
            [
                'class' => AudiobookFixture::class,
                'dataCount' => 40,
            ],
        ];
    }

    public function testPreflight()
    {
        $this->assertPreflightrequest('v1/audiobooks');
    }

    /**
     * @throws Exception
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @depends testPreflight
     */
    public function testListingApiCanBeCalledAnonymously()
    {
        $this->logout();
        $this->apiCall('v1/audiobooks', 'GET');
        $this->assertEquals(200, $this->lastApiCallHttpResponseCode);
    }

    /**
     * @throws Throwable
     * @throws Exception
     * @throws StaleObjectException
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @depends testListingApiCanBeCalledAnonymously
     */
    public function testCanFilterByReleaseYear()
    {
        $YEAR_GOOD = 1991;
        $YEAR_BAD = 1992;

        $audiobook1 = $this->createAudiobook();
        $audiobook2 = $this->createAudiobook();
        $audiobook3 = $this->createAudiobook();
        $audiobook4 = $this->createAudiobook();

        $audiobook1->copyright_year = $YEAR_GOOD;
        $audiobook2->copyright_year = $YEAR_BAD;
        $audiobook3->copyright_year = $YEAR_GOOD;
        $audiobook4->copyright_year = $YEAR_BAD;

        $audiobook1->save(false);
        $audiobook2->save(false);
        $audiobook3->save(false);
        $audiobook4->save(false);

        $audiobooks = $this->apiCall('v1/audiobooks', 'GET', [
            'filter' => [
                'copyright_year' => $YEAR_GOOD
            ]
        ]);

        foreach ($audiobooks['items'] as $audiobook) {
            $this->assertEquals($YEAR_GOOD, $audiobook['year']);
        }

        $audiobook1->delete();
        $audiobook2->delete();
        $audiobook3->delete();
        $audiobook4->delete();
    }

    /**
     * @throws Exception
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @depends testListingApiCanBeCalledAnonymously
     */
    public function testReturnsMandatoryFields()
    {
        $mandatoryFields = [
            'author_name',
            'cover_url',
            'id',
            'language',
            'title',
            'total_seconds',
            'type',
            'year'
        ];
        $this->loadFixtures();

        $audiobooks = $this->apiCall('v1/audiobooks', 'GET');
        foreach ($audiobooks['items'] as $item) {

            foreach ($mandatoryFields as $field) {
                $this->assertArrayHasKey($field, $item, "Mandatory field {$field} not returned");
            }
        }

        $this->unloadFixtures();
    }
}

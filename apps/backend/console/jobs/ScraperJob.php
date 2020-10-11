<?php

namespace console\jobs;


use linslin\yii2\curl\Curl;
use Yii;
use yii\base\Exception;



trait ScraperJob
{

    /**
     * @param $path
     * @return mixed
     * @throws \Exception
     */
    protected function callScraper($path)
    {
        $baseUrl = $this->getScraperBaseUrl();
        $fullUrl = $baseUrl . '/' . $path;

        $curl = new Curl();
        $response = $curl->get($fullUrl, false);

        if ($curl->responseCode >= 400) {
            throw new Exception('Response from scraper expected HTTP code 200. ' . $curl->responseCode . ' received');
        }

        Yii::debug([
            'Response from scraper: ',
            $response
        ]);

        return $response;
    }

    /**
     * @return array|false|string
     * @throws Exception
     */
    protected function getScraperBaseUrl()
    {
        $scraperUrl = getenv('SCRAPER_URL');
        if (!$scraperUrl) {
            $scraperUrl = 'http://scraper:3000';
        }

        return $scraperUrl;
    }

}

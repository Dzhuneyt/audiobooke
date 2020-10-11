<?php

namespace tests\functional;

use common\models\User;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use tests\functional\util\AudiobookTrait;
use tests\helpers\Curl;
use Yii;
use yii\base\Exception;
use yii\helpers\Json;
use yii\test\FixtureTrait;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;



class FunctionalTestCase extends TestCase
{
    use FixtureTrait;
    use AudiobookTrait;

    /**
     * Stores the HTTP code (e.g. 200, 404) from the latest API call that was made
     * @var
     */
    public $lastApiCallHttpResponseCode;
    /**
     * @var User
     */
    protected $baseUser;
    /**
     * @var Generator
     */
    protected $faker;
    private $baseUrl = 'http://127.0.0.1:8080/';
    private $accessToken;

    public function assertPreflightrequest($path)
    {
        $oldToken = $this->accessToken;
        $this->logout();
        try {
            $result = $this->apiCall($path, 'OPTIONS');
            $this->assertNull(
                $result,
                'Preflight request returned some body'
            );
            $this->assertEquals(
                200,
                $this->lastApiCallHttpResponseCode,
                'Preflight request returned a bad HTTP code: ' . $path
            );
        } catch (Exception $exception) {
            $this->fail('Failed making a preflight request for path: ' . $path . '. Error: ' . $exception->getMessage());
        }
        $this->accessToken = $oldToken;
    }

    protected function logout()
    {
        $this->accessToken = null;
    }

    /**
     * @param $path
     * @param string $method
     * @param array $params
     *
     * @return array
     * @throws Exception
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    protected function apiCall($path, $method = 'GET', $params = [])
    {
        $curl = new Curl();
        $curl->setHeader('Accept', 'application/json');

        if ($this->accessToken) {
            $curl->setGetParams(['access-token' => $this->accessToken]);
        }

        $url = $this->baseUrl . $path;
        switch ($method) {
            case 'GET':
                $curl->setGetParams($params);
                $response = $curl->get($url);
                break;
            case 'POST':
                $curl->setPostParams($params);
                $response = $curl->post($url);
                break;
            case 'PUT':
                $curl->setPostParams($params);
                $response = $curl->put($url);
                break;
            case 'DELETE':
                $curl->setPostParams($params);
                $response = $curl->delete($url);
                break;
            case 'OPTIONS':
                $curl->setPostParams([]);
                $response = $curl->options($url);
                break;
            default:
                throw new Exception('HTTP method not implemented for API call');
        }

        if ($response !== false) {
            try {
                $response = Json::decode($response, true);
            } catch (\Exception $e) {
                Yii::error("Can not parse JSON response from API");
                throw $e;
            }
        }

        $this->lastApiCallHttpResponseCode = $curl->responseCode;

        // List of status codes here http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
        switch ($curl->responseCode) {

            case 'timeout':
                throw new Exception('API call resulted in timeout: ' . $path);
            case 200: // success
            case 201: // successfully created
            case 204: // successfully deleted
                return $response;
            case 400:
                throw new BadRequestHttpException('Invalid request to API: ' . $method . ':' . $url . '. Exception: ' . print_r($response['message'],
                        true));
            case 401:
                throw new UnauthorizedHttpException('Attempting to call an authenticated API with no token: ' . $method . " " . $url);
            case 403:
                throw new ForbiddenHttpException('Unauthorized API call: ' . $url . '. Message: ' . $response['message']);
            case 404:
                //404 Error logic here
                throw new NotFoundHttpException("URL not found:" . $path);
            case 422:
                throw new ServerErrorHttpException('Model validation failed - error 422: ' . print_r($response, true));
            case 500:
                throw new ServerErrorHttpException('API call during test threw 500 Internal Server Error: ' . print_r($response,
                        true));
            default:
                echo 'Unknown test failure. Possible bug with testing framework' . PHP_EOL;
                var_dump(['url', $url]);
                var_dump(['method', $method]);
                var_dump(['Response code', $curl->responseCode]);
                var_dump(['Response', $response]);
                throw new ServerErrorHttpException('API call during test resulted in an unknown error code:' . print_r($curl->responseCode,
                        1));
                break;
        }
    }

    protected function setUp()
    {
        parent::setUp();
//        $this->faker = \Faker\Factory::create();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

}

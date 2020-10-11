<?php

namespace rest\versions\v1;

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Module;



class RestModule extends Module implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $app->urlManager->addRules([
            [
                'class' => 'yii\rest\UrlRule',
                'controller' => [
                    $this->id . '/healthcheck',
                ],
                'extraPatterns' => [
                    'GET isalive' => 'isalive',
                ]
            ],
            [
                'class' => 'yii\rest\UrlRule',
                'controller' => [
                    $this->id . '/audiobook',
                ],
                'extraPatterns' => [
                    'download/{id}' => 'download', // GET
                    'PUT favorite/{id}' => 'favorite',
                    'OPTIONS <action:\w+>' => 'options',
                    'OPTIONS download/{id}' => 'options',
                    'OPTIONS favorite/{id}' => 'options',
                    'topten' => 'topten', // GET
                ]
            ],
            [
                'class' => 'yii\rest\UrlRule',
                'controller' => [
                    $this->id . '/author',
                ],
                'extraPatterns' => [
                    'latest' => 'latest', // GET
                    'OPTIONS <action:\w+>' => 'options',
                ]
            ],
            [
                'class' => 'yii\rest\UrlRule',
                'controller' => [
                    $this->id . '/user',
                ],
                'pluralize' => false,
                'extraPatterns' => [
                    'GET sso' => 'sso',
                    'OPTIONS <action:\w+>' => 'options',
                ]
            ],
        ]);
    }
}

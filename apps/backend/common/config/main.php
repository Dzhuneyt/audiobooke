<?php

use yii\caching\DummyCache;
use yii\mutex\MysqlMutex;
use yii\queue\db\Queue;
use yii\queue\LogBehavior;
use yii\web\User;



return [
    'id' => 'audiobooke',
    'language' => 'en',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'bootstrap' => [
        'queue', // The component registers its own console commands
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\DummyCache',
        ],
        'user' => [
            'class' => 'yii\web\User'
        ],
        'assetManager' => [
            'basePath' => '@app/web/assets',
            'baseUrl' => '@web/debug/assets',
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,   // do not publish the bundle
                    'js' => [
                        '//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
                    ]
                ],
            ],
            'assetMap' => [
                'jquery.js' => '//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js',
            ],
        ],
        // Use DB queue for local development
        'queue' => [
            'class' => Queue::class,
            'db' => 'db', // DB connection component or its config
            'as log' => LogBehavior::class,
            'tableName' => '{{%queue}}', // Table name
            'channel' => 'default', // Queue channel key
            'mutex' => MysqlMutex::class, // Mutex used to sync queries
            // Other driver options
            'ttr' => 5 * 60, // Max time for job execution
            'attempts' => 3, // Max number of attempts
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
            'charset' => 'utf8',
        ],
        'log' => [
            'traceLevel' => 3,
            'targets' => [
                'sql_profiler' => [
                    'class' => 'codemix\streamlog\Target',
                    'url' => 'php://stdout',
                    'logVars' => [],
                    'levels' => ['profile', 'info', 'error'],
                    'categories' => ['yii\db\Command::query', 'yii\db\Command::execute'],
                    'prefix' => function ($message) {
                        return '';
                    },
                    'enabled' => false, // too noisy, only enable on demand
                ],

                // These two write to the Docker container logs stream
                'docker_tracer' => [
                    'class' => 'codemix\streamlog\Target',
                    'url' => 'php://stdout',
                    'levels' => ['info', 'trace'],
                    'logVars' => [],
                    'enabled' => false, // too noisy
                ],
                'docker_errors' => [
                    'class' => 'codemix\streamlog\Target',
                    'url' => 'php://stderr',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],
            ],

        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'encryption' => 'tls',
                'host' => getenv('SMTP_HOST'),
                'port' => getenv('SMTP_PORT'),
                'username' => getenv('SMTP_USER'),
                'password' => getenv('SMTP_PASSWORD'),
            ],
        ],
    ],
    'params' => [
        'GOOGLE_CLIENT_ID' => getenv('GOOGLE_CLIENT_ID'),
        'GOOGLE_CLIENT_SECRET' => getenv('GOOGLE_CLIENT_SECRET'),
        'GOOGLE_SSO_REDIRECT_URL' => getenv('GOOGLE_SSO_REDIRECT_URL'),
    ],
];

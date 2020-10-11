<?php
/**
 * Local config for developer of environment.
 */

use yii\caching\DummyCache;
use yii\log\DbTarget;
use yii\mutex\MysqlMutex;
use yii\queue\db\Queue;
use yii\queue\LogBehavior;



return [
    'language' => 'en',
    'components' => [
        'cache' => [
            'class' => DummyCache::class
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
            'charset' => 'utf8',
        ],
        'log' => [
            'targets' => [
                'error_log' => [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/error.log',
                    'logVars' => [],
                    'levels' => ['error'],
                ],
                'sql_profiler' => [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/profile.log',
                    'logVars' => [],
                    'levels' => ['profile', 'info', 'error'],
                    'categories' => ['yii\db\Command::query', 'yii\db\Command::execute'],
                    'enabled' => true,
                ],
                'db' => [
                    'class' => DbTarget::class,
                    'levels' => ['warning', 'error'],
                    'logVars' => [],
                    // Don't log anything to DB during development
                    'enabled' => false,
                ],
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
    ],
    'params' => [
        'GOOGLE_CLIENT_ID' => getenv('GOOGLE_CLIENT_ID'),
        'GOOGLE_CLIENT_SECRET' => getenv('GOOGLE_CLIENT_SECRET'),
        'GOOGLE_SSO_REDIRECT_URL' => getenv('GOOGLE_SSO_REDIRECT_URL'),
    ],
];

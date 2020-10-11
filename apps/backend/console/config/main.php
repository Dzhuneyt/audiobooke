<?php

use console\controllers\FixtureController;



$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);
$config = [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'fixture' => 'console\controllers\FixtureController',
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => [
                '@app/migrations',
                '@tecnocen/oauth2server/migrations/tables',
//                '@yii/log/migrations/',
            ],
            'migrationNamespaces' => [
                'yii\queue\db\migrations'
            ],
            //'migrationPath' => null, // allows to disable not namespaced migration completely
        ],
    ],
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];

return $config;

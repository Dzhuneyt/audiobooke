<?php

use common\models\User;
use yii\caching\DbCache;
use yii\web\JsonResponseFormatter;



$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
//    'id' => 'rest-api',
//    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'oauth2', // Required for automatically registering URL rules
        'v1',
    ],
    'modules' => [
        'v1' => [
            'class' => 'rest\versions\v1\RestModule'
        ],
        'v2' => [
            'class' => 'rest\versions\v2\RestModule'
        ],

        'oauth2' => [
            'class' => 'tecnocen\oauth2server\Module',
            'tokenParamName' => 'access-token',
            'tokenAccessLifetime' => 3600 * 24 * 7, // 7 days
            'storageMap' => [
                'user_credentials' => 'common\models\User',
            ],
            'grantTypes' => [
                'user_credentials' => [
                    'class' => 'OAuth2\GrantType\UserCredentials',
                ],
                'refresh_token' => [
                    'class' => 'OAuth2\GrantType\RefreshToken',
                ]
            ]
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'formatters' => [
                'json' => [
                    'class' => JsonResponseFormatter::class,
                    'prettyPrint' => true,
                ],
            ],
        ],
        'log' => [
            'targets' => [
            ],
        ],
        'request' => [
            'class' => '\yii\web\Request',
            'enableCookieValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
        ],
    ],
    'params' => $params,
];

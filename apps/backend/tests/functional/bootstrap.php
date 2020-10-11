<?php

use yii\helpers\ArrayHelper;



error_reporting(E_ALL);
define('YII_DEBUG', true);
define('YII_ENV', 'dev');
require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../../console/config/bootstrap.php');

//$config = array_merge_recursive(
//    requireIfExists(__DIR__ . '/../../common/config/main.php'),
//    requireIfExists(__DIR__ . '/../../common/config/main-local.php'),
//    requireIfExists(__DIR__ . '/../../rest/config/main.php'),
//    requireIfExists(__DIR__ . '/../../rest/config/main-local.php'),
//    requireIfExists(__DIR__ . '/../../common/config/tests.php'),
//    requireIfExists(__DIR__ . '/../../common/config/tests-local.php'),
//    requireIfExists(__DIR__ . '/../../console/config/main.php')
//);

$config2 = yii\helpers\ArrayHelper::merge(
    requireIfExists(__DIR__ . '/../../common/config/main.php'),
    requireIfExists(__DIR__ . '/../../common/config/main-local.php'),
    requireIfExists(__DIR__ . '/../../console/config/main.php'),
//    requireIfExists(__DIR__ . '/../../common/config/main-local.php'),
//    requireIfExists(__DIR__ . '/../../rest/config/main.php'),
//    requireIfExists(__DIR__ . '/../../rest/config/main-local.php'),
    requireIfExists(__DIR__ . '/../../common/config/tests.php'),
    requireIfExists(__DIR__ . '/../../common/config/tests-local.php')
);

try {
    $consoleApp = new yii\console\Application($config2);
    // Execute migrations every time before tests
    $consoleApp->runAction('migrate/fresh', [
        'interactive' => '0',
        'compact' => '1',
    ]);
} catch (Exception $e) {
    echo '--------------';
    echo 'Can not run functional tests due to a failure with migrations' . PHP_EOL;
    echo '--------------';
    throw $e;
}

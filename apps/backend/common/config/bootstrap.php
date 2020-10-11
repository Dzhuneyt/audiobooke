<?php
$ds = DIRECTORY_SEPARATOR;
Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('rest', dirname(dirname(__DIR__)) . '/rest');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('app/web', dirname(dirname(__DIR__)) . '/rest/web');
Yii::setAlias('fixtures', dirname(dirname(__DIR__)) . '/fixtures');
Yii::setAlias('tests', dirname(dirname(__DIR__)) . '/tests');
Yii::setAlias('@root', dirname(dirname(dirname(dirname(__DIR__)))));

// Load env files from .env
$envFile = Yii::getAlias('@root/.env');
if (is_file($envFile)) {
    $dotenv = Dotenv\Dotenv::create(Yii::getAlias('@root'));
    $envData = $dotenv->load();
}

function requireIfExists($file)
{
    if (!is_file($file)) {
        return [];
    }
    return require($file);
}

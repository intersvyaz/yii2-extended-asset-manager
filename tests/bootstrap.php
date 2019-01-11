<?php
error_reporting(-1);

define('YII_ENABLE_ERROR_HANDLER', false);
define('YII_DEBUG', true);
define('YII_ENV', 'test');

// require composer autoloader if available
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

Yii::setAlias('@yiiunit', __DIR__);
Yii::setAlias('@runtime', __DIR__ . '/runtime');
Yii::setAlias('@webroot/assets', __DIR__ . '/runtime/assets');
Yii::setAlias('@web/assets', __DIR__ . '/runtime/assets');

new \yii\web\Application([
    'id' => 'testapp',
    'basePath' => __DIR__,
    'vendorPath' => __DIR__ . '/../vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'request' => [
            'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
            'scriptFile' => __DIR__ . '/index.php',
            'scriptUrl' => '/index.php',
        ],
    ],
]);

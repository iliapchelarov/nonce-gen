<?php
define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require __DIR__ .'/../vendor/autoload.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../config/console.php',
    [
        'components' => [
            'db' => require __DIR__ . '/../config/test_db.php'
        ]
    ]
);


$application = new yii\console\Application($config);
$application->init();

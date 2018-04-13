<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
        '@inpsyde'  => '@app/src/inpsyde'
    ],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'mutex' => [
            'class' => 'yii\mutex\MysqlMutex',
        ],
        'nonceGen' => [
            'class' => 'inpsyde\nonce\SimpleNonceGenerator',
            'min'    => 10000,
            'max'    => 99999,
//             'class' => 'inpsyde\nonce\WPNonceGenerator',
//             'salt' => '[PIj c-w74c=53874=5q03t-w uvrpaojdhfAUR HPIEAUR HPIDHUVFU_)*&@)*&!+$#)(&%',
            'timeout' => 3600 * 24, //seconds
        ],
    ],
    'params' => $params,
    
    'controllerMap' => [
        'inonce' => [
            'class'  => 'app\commands\InonceController',
        ],
    ],
];

return $config;

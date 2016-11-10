<?php return \yii\helpers\ArrayHelper::merge(require(__DIR__ . '/base.php'), [
    'id' => 'yii2-empty-console',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/console.log',
                ],
            ],
        ],
    ],
], require(__DIR__ . '/autoload.php'), is_file(__DIR__ . '/override.php') ? require(__DIR__ . '/override.php') : []);
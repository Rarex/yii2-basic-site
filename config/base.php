<?php return [
    'basePath' => '@app',
    'timeZone' => 'Europe/Moscow',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'urlManager' => [
            'class' => \yii\web\UrlManager::class,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
    'params' => [
        'name' => 'Yii2 Empty',
    ],
];
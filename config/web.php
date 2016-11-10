<?php return \yii\helpers\ArrayHelper::merge(require(__DIR__ . '/base.php'), [
    'id' => 'yii2-empty-web',
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'e74471db7e6656b8fc07a2a090809686',
        ],
        'assetManager' => [
            'class' => \yii\web\AssetManager::class,
            'linkAssets' => YII_ENV_PROD,
        ],
        'errorHandler' => [
            'errorAction' => 'site/default/error',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/web.log',
                ],
            ],
        ],
        'session' => [
            'class' => \app\base\Session::class,
            'name' => '_user_session',
            'savePath' => '@runtime/session',
        ],
        'mailer' => [
            'class' => \yii\swiftmailer\Mailer::class,
            'useFileTransport' => true,
        ],
    ],
    'on beforeRequest' => function () {
        // SEO: redirect "/page/" to "/page" instead 404 error
        $pathInfo = Yii::$app->getRequest()->getPathInfo();
        if ($pathInfo && substr($pathInfo, -1) === '/') {
            Yii::$app->getResponse()->redirect('/' . rtrim($pathInfo, '/'), 301);
            Yii::$app->end();
        }
    },
], require(__DIR__ . '/autoload.php'), is_file(__DIR__ . '/override.php') ? require(__DIR__ . '/override.php') : []);
<?php

$env = is_file(__DIR__ . '/../config/env.php') ? require(__DIR__ . '/../config/env.php') : 'local';

defined('YII_DEBUG') or define('YII_DEBUG', $env !== 'prod');
defined('YII_ENV') or define('YII_ENV', $env);
defined('YII_ENV_LOCAL') or define('YII_ENV_LOCAL', YII_ENV === 'local');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@app', dirname(__DIR__));
$config = is_file(__DIR__ . '/../runtime/web.php') ? require(__DIR__ . '/../runtime/web.php') : require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();

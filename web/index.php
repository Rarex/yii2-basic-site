<?php
$env = is_file(__DIR__ . '/../config/env.php') ? require(__DIR__ . '/../config/env.php') : 'local';

defined('YII_DEBUG') or define('YII_DEBUG', $env !== 'prod');
defined('YII_ENV') or define('YII_ENV', $env);

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();

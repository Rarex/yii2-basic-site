<?php

$dynamic = [];
foreach (new DirectoryIterator(__DIR__ . '/autoload') as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $dynamic = \yii\helpers\ArrayHelper::merge($dynamic, require($file->getRealPath()));
    }
}

return $dynamic;
<?php

namespace app\base;

class Session extends \yii\web\Session
{
    /**
     * @var string
     */
    public $savePathMode = '0777';

    /**
     * @inheritdoc
     */
    public function setSavePath($value)
    {
        $path = \Yii::getAlias($value);

        if (!is_dir($path)) {
            @mkdir($path, octdec($this->savePathMode), true);
            @chmod($path, octdec($this->savePathMode));
        }

        parent::setSavePath($value);
    }
}
<?php

namespace app\modules\site\controllers;

use app\modules\site\base\SiteController;

class DefaultController extends SiteController
{
    public $layout = 'main';

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}

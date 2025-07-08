<?php

namespace app\modules\apiv1\controllers;

use yii\web\Controller;

/**
 * Default controller for the `apiv1` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}

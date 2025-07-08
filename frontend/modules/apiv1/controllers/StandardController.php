<?php

namespace frontend\modules\apiv1\controllers;

use app\models\Standards;
use yii\rest\ActiveController;

class StandardController extends ActiveController
{
    public $modelClass = Standards::class;


}
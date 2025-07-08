<?php

namespace app\modules\apiv1\controllers;

use app\models\Clause;
use yii\rest\ActiveController;

class ClauseController extends ActiveController
{
    public $modelClass = Clause::class;


}
<?php

namespace app\modules\apiv1\controllers;

use app\models\Requirements;
use yii\rest\ActiveController;

class RequirementsController extends ActiveController
{
    public $modelClass = Requirements::class;

}
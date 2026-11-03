<?php
namespace app\modules\apiv1\controllers;

use app\models\Requirements;

class RequirementsController extends BaseApiController
{
    public $modelClass = Requirements::class;

    public $permissionEntity = 'requirement';
}

<?php

namespace app\modules\apiv1\controllers;

use app\models\Standards;

class StandardController extends BaseApiController
{
    public $modelClass = Standards::class;

    public $permissionEntity = 'standard';
}

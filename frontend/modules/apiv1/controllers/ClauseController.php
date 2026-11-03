<?php

namespace app\modules\apiv1\controllers;

use app\models\Clause;

class ClauseController extends BaseApiController
{
    public $modelClass = Clause::class;

    public $permissionEntity = 'clause';
}

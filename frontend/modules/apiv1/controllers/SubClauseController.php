<?php

namespace app\modules\apiv1\controllers;

use app\models\SubClause;

class SubClauseController extends BaseApiController
{
    public $modelClass = SubClause::class;

    public $permissionEntity = 'sub-clause';
}

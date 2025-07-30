<?php

namespace common\events;

class RequirementsStatusEvent extends \yii\base\Event
{
    public $requirement_id;
    public $sub_clause_id;
    public $status;

    public $assignee;
}
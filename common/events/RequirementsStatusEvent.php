<?php

namespace common\events;

class RequirementsStatusEvent extends \yii\base\Event
{
    public $sub_clause_id;
    public $status;
}
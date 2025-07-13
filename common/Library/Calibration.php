<?php
namespace common\Library;

use app\models\Requirements;
use app\models\SubClause;
use Yii;
use yii\base\Event;
use yii\base\Component;
use app\models\Contracts;
use app\models\WorkflowEntries;


class Calibration extends Component
{
    /**
     * Attaches an event handler during initiation
     */

    public function init()
    {
        parent::init();

        // Listen to custom event "EVENT_STATUS_PENDING" from WorkflowEntries model
        Event::on(
            Requirements::class,
            Requirements::EVENT_EVAL_STATUS,
            [$this, 'handlerRequirementStatusChanged']
        );

    }

    public function handlerRequirementStatusChanged(Event $event)
    {
        $clause = $event->sub_clause_id; // sub_clause identifier

        // Save average status of all requirements per sub_clause
        $sub_clause = SubClause::findOne($clause);
        $sub_clause->average_status = $sub_clause->getAverageStatus();
        $sub_clause->save(false);
    }





}


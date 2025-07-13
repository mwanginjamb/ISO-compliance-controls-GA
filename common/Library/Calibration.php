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

        // Listen to custom event "EVENT_EVAL_STATUS" from WorkflowEntries model
        Event::on(
            Requirements::class,
            Requirements::EVENT_EVAL_STATUS,
            [$this, 'handlerRequirementStatusChanged']
        );

    }

    public function handlerRequirementStatusChanged(Event $event)
    {
        Yii::info('Handling event: ' . Requirements::EVENT_EVAL_STATUS, 'calibration');
        $subclause = $event->sub_clause_id; // sub_clause identifier
        Yii::info('subclause: ' . $subclause, 'calibration');

        // Save average status of all requirements per sub_clause
        $subClause = SubClause::findOne($subclause);
        $subClause->average_status = $subClause->getAverageStatus();
        if (!$subClause->save(false)) {
            // Log possible error and ensure they can be rendered to avoid array to string conversion error
            $errors = print_r($subClause->errors, true);
            Yii::error('subclause update error: ' . $errors, 'calibration');
        }
    }





}


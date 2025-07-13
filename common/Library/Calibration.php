<?php
namespace common\Library;

use app\models\Requirements;
use app\models\SubClause;
use common\events\RequirementsStatusEvent;
use Yii;
use yii\base\Event;
use yii\base\Component;



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

    public function handlerRequirementStatusChanged(RequirementsStatusEvent $event)
    {
        // Log relevant event properties instead of the whole object
        Yii::info('Handling event. Event name: ' . $event->name . ', sender class: ' . get_class($event->sender) . ', sub_clause_id: ' . ($event->sub_clause_id ?? 'N/A'), 'calibration');
        $subclauseId = $event->sub_clause_id; // sub_clause identifier
        Yii::info('Subclause ID: ' . $subclauseId, 'calibration');

        // Save average status of all requirements per sub_clause
        $subClause = SubClause::findOne($subclauseId);
        if ($subClause) {
            $subClause->average_status = $subClause->getAverageStatus();
            if ($subClause->save(false)) {
                // Log the updated SubClause object's public properties as JSON
                Yii::info('Subclause update: ' . json_encode($subClause->toArray()), 'calibration');
            } else {
                // Log validation errors as JSON
                Yii::error('Subclause update error: ' . json_encode($subClause->getErrors()), 'calibration');
            }
        } else {
            Yii::error('Subclause not found for ID: ' . $subclauseId, 'calibration');
        }

    }





}


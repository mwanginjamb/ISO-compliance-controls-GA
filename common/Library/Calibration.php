<?php
namespace common\Library;

use Yii;
use yii\base\Event;
use yii\base\Component;
use app\models\SubClause;
use yii\helpers\VarDumper;
use app\models\Requirements;
use common\events\RequirementsStatusEvent;



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

        // Listen for custom event "EVENT_ASSIGNMENT" from WorkflowEntries model
        Event::on(
            Requirements::class,
            Requirements::EVENT_ASSIGNMENT,
            [$this, 'handlerAssignmentChanged']
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


    // Assignment Event handler

    public function handlerAssignmentChanged(RequirementsStatusEvent $event)
    {
        // Log relevant event properties instead of the whole object
        Yii::info('Handling event. Event name: ' . $event->name . ', sender class: ' . get_class($event->sender) . ', Assignee: ' . ($event->assignee ?? 'N/A') . ', sub_clause_id: ' . ($event->sub_clause_id ?? 'N/A'), 'calibration');
        // Send an email to the assignee with details of the requirment and sub_clause and 
        $requirement = Requirements::findOne($event->requirement_id);
        $subClause = $requirement->subClause;
        $clause = $subClause->clause;

        // Get the assignee email address
        if ($event->assignee) {
            list($no, $email, $name) = explode(' - ', $event->assignee);
            // Send an email to the assignee with details of the requirment and sub_clause and clause
            $this->sendNotification($email, $name, $clause, $subClause, $requirement);
        }

    }

    public function sendNotification($assigneeEmail, $assigneeName, $clause, $subClause, $requirement)
    {

        // Construct the email content based on payment line data
        $subject = "COMPLIANCE IMS Notification for Gap Analysis Task Assignment";
        try {
            // Use Yii's mailer component to send the email
            $mail = Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'ims-html', 'text' => 'ims-text'],
                    ['clause' => $clause, 'assigneeName' => $assigneeName, 'subClause' => $subClause, 'requirement' => $requirement]
                )
                ->setFrom(env('SMTP_USERNAME'))
                ->setTo($assigneeEmail)  // Assuming each line has an associated customer email
                ->setSubject($subject)
                ->send();
            Yii::info('Notification mailed successfully : ' . VarDumper::dumpAsString($mail), 'calibration');
            return true;
        } catch (\Exception $e) {
            // Log and return the error message
            Yii::error("Error sending email for requirement #" . $requirement->id . ": " . $e->getMessage(), 'calibration');
            return ['status' => 'failure', 'requirement' => $requirement->id, 'error' => $e->getMessage()];
        }
    }





}


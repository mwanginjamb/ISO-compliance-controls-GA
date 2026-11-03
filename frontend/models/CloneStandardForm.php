<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use app\models\Clause;
use app\models\SubClause;
use app\models\Standards;
use app\models\Requirements;

/**
 * Clones a standard's gap analysis (clauses, sub-clauses / controls and
 * requirements) into a new standard for another assessment period.
 *
 * By default the new period starts unassessed: requirement statuses are reset
 * to "Not Implemented" and evidence, gaps, actions, assignees and timelines are
 * left blank. Either group can be carried over as a baseline.
 */
class CloneStandardForm extends Model
{
    public $standard;
    public $period;
    public $carryOverResults = false;
    public $carryOverAssignments = false;

    /** @var Standards */
    private $_source;

    public function __construct(Standards $source, $config = [])
    {
        $this->_source = $source;
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        $this->standard = $this->_source->standard;
    }

    public function rules()
    {
        return [
            [['standard', 'period'], 'filter', 'filter' => 'strip_tags'],
            [['standard', 'period'], 'trim'],
            [['standard', 'period'], 'required'],
            ['standard', 'string', 'max' => 250],
            ['period', 'string', 'max' => 50],
            ['standard', 'unique', 'targetClass' => Standards::class, 'targetAttribute' => 'standard',
                'message' => 'A standard with this name already exists. Include the new period in the name.'],
            [['carryOverResults', 'carryOverAssignments'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'standard' => 'New gap analysis name',
            'period' => 'Assessment period',
            'carryOverResults' => 'Carry over assessment results (status, evidence, gaps, actions required)',
            'carryOverAssignments' => 'Carry over assignees and timelines',
        ];
    }

    public function getSource()
    {
        return $this->_source;
    }

    /**
     * @return Standards|null the new standard, or null if validation or saving failed
     */
    public function cloneStandard()
    {
        if (!$this->validate()) {
            return null;
        }

        $source = $this->_source;
        $clauses = $source->getClauses()->with('subClauses.requirements')->all();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $clone = new Standards([
                'standard' => $this->standard,
                'period' => $this->period,
                'tenant_id' => $source->tenant_id,
                'cloned_from_id' => $source->id,
            ]);
            $clone->save(false);

            foreach ($clauses as $clause) {
                $newClause = new Clause();
                $newClause->setAttributes($this->copyable($clause), false);
                $newClause->standard_id = $clone->id;
                $newClause->save(false);

                foreach ($clause->subClauses as $subClause) {
                    $newSubClause = new SubClause();
                    $newSubClause->setAttributes($this->copyable($subClause, ['number', 'average_status']), false);
                    $newSubClause->clause_id = $newClause->id;
                    $newSubClause->average_status = $this->carryOverResults ? $subClause->average_status : 0;
                    $newSubClause->save(false); // afterSave() assigns the new number

                    foreach ($subClause->requirements as $requirement) {
                        $newRequirement = new Requirements([
                            'sub_clause_id' => $newSubClause->id,
                            'description' => $requirement->description,
                            'status' => $this->carryOverResults ? $requirement->status : 0,
                        ]);
                        if ($this->carryOverResults) {
                            $newRequirement->evidence_path = $requirement->evidence_path;
                            $newRequirement->gaps = $requirement->gaps;
                            $newRequirement->actions_required = $requirement->actions_required;
                        }
                        if ($this->carryOverAssignments) {
                            $newRequirement->assignee = $requirement->assignee;
                            $newRequirement->timeline = $requirement->timeline;
                        }
                        $newRequirement->save(false);
                    }
                }
            }

            $transaction->commit();
            return $clone;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error('Cloning standard #' . $source->id . ' failed: ' . $e->getMessage(), __METHOD__);
            $this->addError('standard', 'The gap analysis could not be cloned. Please try again or contact support.');
            return null;
        }
    }

    /**
     * Attributes of $record to copy, excluding keys and audit columns.
     */
    private function copyable($record, $exclude = [])
    {
        return $record->getAttributes(null, array_merge(
            ['id', 'standard_id', 'clause_id', 'created_at', 'updated_at', 'created_by', 'updated_by'],
            $exclude
        ));
    }
}

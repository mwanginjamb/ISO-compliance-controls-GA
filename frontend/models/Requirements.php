<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "requirements".
 *
 * @property int $id
 * @property string|null $description
 * @property int|null $status
 * @property string|null $evidence_path
 * @property string|null $gaps
 * @property string|null $actions_required
 * @property int|null $sub_clause_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property SubClause $subClause
 */
class Requirements extends \yii\db\ActiveRecord
{
    const EVENT_EVAL_STATUS = 'eval_status';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'requirements';
    }



    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'status', 'evidence_path', 'gaps', 'actions_required', 'sub_clause_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['description', 'gaps', 'actions_required'], 'string'],
            [['status', 'sub_clause_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['evidence_path'], 'string', 'max' => 350],
            [['sub_clause_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubClause::class, 'targetAttribute' => ['sub_clause_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'evidence_path' => Yii::t('app', 'Evidence Path'),
            'gaps' => Yii::t('app', 'Gaps'),
            'actions_required' => Yii::t('app', 'Actions Required'),
            'sub_clause_id' => Yii::t('app', 'Sub Clause ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[SubClause]].
     *
     * @return \yii\db\ActiveQuery|SubClauseQuery
     */
    public function getSubClause()
    {
        return $this->hasOne(SubClause::class, ['id' => 'sub_clause_id']);
    }

    /**
     * {@inheritdoc}
     * @return RequirementsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RequirementsQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert && $this->status) { // not an insert and status has a value
            if (array_key_exists('status', $changedAttributes)) { // status has changed
                $this->trigger(self::EVENT_EVAL_STATUS); // trigger the event
                // log the event and its data
                Yii::info('Event triggered: ' . self::EVENT_EVAL_STATUS, 'calibration');
            }
        }
    }

}

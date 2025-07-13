<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "sub_clause".
 *
 * @property int $id
 * @property string|null $number
 * @property string|null $sub_clause
 * @property int|null $clause_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $average_status
 *
 * @property Clause $clause
 * @property Requirements[] $requirements
 * @property int|null $averageStatus
 */
class SubClause extends \yii\db\ActiveRecord
{


    public $averageStatus;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sub_clause';
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
            [['clause_id', 'sub_clause'], 'required'],
            [['number', 'sub_clause', 'clause_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['sub_clause'], 'string'],
            [['clause_id', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['number'], 'string', 'max' => 250],
            ['number', 'unique'],
            [['number'], 'required', 'on' => 'update'],
            ['averageStatus', 'integer'],// calculated value
            ['average_status', 'decimal'], // DB column - populated from calibration event handler
            [['clause_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clause::class, 'targetAttribute' => ['clause_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'number' => Yii::t('app', 'Number'),
            'sub_clause' => Yii::t('app', 'Sub Clause'),
            'clause_id' => Yii::t('app', 'Clause ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[Clause]].
     *
     * @return \yii\db\ActiveQuery|ClauseQuery
     */
    public function getClause()
    {
        return $this->hasOne(Clause::class, ['id' => 'clause_id']);
    }

    /**
     * Gets query for [[Requirements]].
     *
     * @return \yii\db\ActiveQuery|RequirementsQuery
     */
    public function getRequirements()
    {
        return $this->hasMany(Requirements::class, ['sub_clause_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return SubClauseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SubClauseQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        // 'number' field is auto-generated  - like {$this->clause}.{this->id} - lastInsertedId
        if ($insert) {

            $this->number = $this->clause_id . '.' . $this->id;
            $this->scenario = 'update';
            $this->save();
        }
    }

    // Get Average 'Status' from all related requirements, it shold be rounded to the nearest integer

    public function getAverageStatus()
    {
        $this->averageStatus = $this->getRequirements()->count() > 0 ? $this->getRequirements()->average('status') : 0;
        return Yii::$app->formatter->asDecimal($this->averageStatus, Yii::$app->params['decimalPlaces']);
    }

    public function getVerdict()
    {
        $average = $this->getAverageStatus();
        return Yii::$app->utility->getDescriptiveStatusFromConfig($average);
    }

    // Badge Backgrounds
    public function getBadge()
    {
        $average = $this->getAverageStatus();
        return Yii::$app->utility->getBadgeFromConfig($average);
    }


}

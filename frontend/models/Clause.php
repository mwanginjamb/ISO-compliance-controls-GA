<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "clause".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $standard_id
 *
 * @property Standards $standards
 * @property SubClause[] $subClauses
 */
class Clause extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clause';
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
            [['title', 'standard_id'], 'required'],
            ['title', 'unique'],
            [['title', 'description', 'created_at', 'updated_at', 'created_by', 'updated_by', 'standard_id'], 'default', 'value' => null],
            [['description'], 'string'],
            [['created_at', 'updated_at', 'created_by', 'updated_by', 'standard_id'], 'integer'],
            [['title'], 'string', 'max' => 250],
            [['standard_id'], 'exist', 'skipOnError' => true, 'targetClass' => Standards::class, 'targetAttribute' => ['standard_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'standard_id' => Yii::t('app', 'Standard ID'),
        ];
    }

    /**
     * Gets query for [[Standards]].
     *
     * @return \yii\db\ActiveQuery|StandardsQuery
     */
    public function getStandards()
    {
        return $this->hasOne(Standards::class, ['id' => 'standard_id']);
    }

    /**
     * Gets query for [[SubClauses]].
     *
     * @return \yii\db\ActiveQuery|SubClauseQuery
     */
    public function getSubClauses()
    {
        return $this->hasMany(SubClause::class, ['clause_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return ClauseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClauseQuery(get_called_class());
    }

}

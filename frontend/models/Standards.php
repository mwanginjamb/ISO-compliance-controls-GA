<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "standards".
 *
 * @property int $id
 * @property string|null $standard
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property Clause[] $clauses
 */
class Standards extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'standards';
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
            [['standard'], 'required'],
            [['standard', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'default', 'value' => null],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['standard'], 'string', 'max' => 250],
            [['standard'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'standard' => Yii::t('app', 'Standard'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[Clauses]].
     *
     * @return \yii\db\ActiveQuery|ClauseQuery
     */
    public function getClauses()
    {
        return $this->hasMany(Clause::class, ['standard_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return StandardsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StandardsQuery(get_called_class());
    }

}

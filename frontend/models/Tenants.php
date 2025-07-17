<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "tenants".
 *
 * @property int $id
 * @property string $name
 * @property string|null $database_name
 * @property string|null $unique_identifier
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Tenants extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tenants';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['database_name', 'unique_identifier', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['name'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['database_name'], 'string', 'max' => 100],
            [['unique_identifier'], 'string', 'max' => 256],
            [['name'], 'unique'],
            [['database_name'], 'unique'],
            [['unique_identifier'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'database_name' => Yii::t('app', 'Database Name'),
            'unique_identifier' => Yii::t('app', 'Unique Identifier'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return TenantsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TenantsQuery(get_called_class());
    }

}

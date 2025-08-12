<?php

namespace app\models;

use yii\base\Model;
use Yii;
use yii\rbac\DbManager;

class PermissionForm extends Model
{
    public $name;
    public $description;
    public $parent;
    public $originalName;

    public function scenarios()
    {
        return [
            'create' => ['name', 'description', 'parent'],
            'update' => ['name', 'description', 'parent', 'originalName'],
        ];
    }

    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['name'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 255],
            ['parent', 'exist', 'targetClass' => '\yii\rbac\Permission', 'targetAttribute' => 'name'],
            ['name', 'validateUniqueName', 'on' => 'create'],
            ['name', 'validateRenamedPermission', 'on' => 'update'],
        ];
    }

    public function validateUniqueName($attribute)
    {
        $auth = new DbManager();
        if ($auth->getPermission($this->$attribute)) {
            $this->addError($attribute, 'This permission name already exists');
        }
    }

    public function validateRenamedPermission($attribute)
    {
        if ($this->name !== $this->originalName) {
            $auth = new DbManager();
            if ($auth->getPermission($this->name)) {
                $this->addError($attribute, 'This permission name already exists');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Permission Name',
            'description' => 'Description',
            'parent' => 'Parent Permission',
        ];
    }
}
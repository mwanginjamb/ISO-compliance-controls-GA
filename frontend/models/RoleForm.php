<?php
namespace frontend\models;

use yii\base\Model;
use Yii;
use yii\rbac\DbManager;

class RoleForm extends Model
{
    public $role_name;
    public $permissions = [];

    public $originalName; // Add this property


    public function scenarios()
    {
        return [
            'create' => ['role_name', 'permissions'],
            'update' => ['role_name', 'permissions', 'originalName'],
        ];
    }

    public function rules()
    {
        return [
            [['originalName'], 'safe'],
            [['role_name', 'permissions'], 'safe'],
            [['role_name'], 'required'],
            [['role_name'], 'string', 'max' => 64],
            ['role_name', 'validateUniqueRole', 'on' => 'create'],
            ['role_name', 'validateRenamedRole', 'on' => 'update'],
            ['permissions', 'each', 'rule' => ['string']],
        ];
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->permissions = [];
    }

    public function validateUniqueRole($attribute)
    {
        $auth = new DbManager();
        if ($auth->getRole($this->$attribute)) {
            $this->addError($attribute, 'This role name already exists');
        }
    }

    public function validateRenamedRole($attribute)
    {
        if ($this->role_name !== $this->originalName) {
            $auth = new DbManager();
            if ($auth->getRole($this->role_name)) {
                $this->addError($attribute, 'This role name already exists');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'role_name' => 'Role Name',
            'permissions' => 'Permissions',
        ];
    }
}
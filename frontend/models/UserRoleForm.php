<?php
namespace app\models;

use yii\base\Model;
use Yii;
use yii\rbac\DbManager;

class UserRoleForm extends Model
{
    public $userId;
    public $roles = [];

    public function rules()
    {
        return [
            [['userId'], 'required'],
            [['userId'], 'integer'],
            ['roles', 'each', 'rule' => ['string']],
            ['roles', 'default', 'value' => []],
            ['roles', 'validateRoles']
        ];
    }

    public function validateRoles($attribute)
    {
        $auth = new DbManager();
        foreach ($this->$attribute as $roleName) {
            if (!$auth->getRole($roleName)) {
                $this->addError($attribute, "Invalid role: $roleName");
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'roles' => 'Assigned Roles'
        ];
    }
}
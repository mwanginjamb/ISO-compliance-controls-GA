<?php

use yii\db\Migration;

/**
 * Creates the RBAC permissions enforced by the frontend controllers and API.
 *
 * Idempotent: existing roles/permissions are reused, missing ones are created.
 * All permissions are granted to `compliance-admin`; `compliance-officer`
 * gets the requirement-assessment permissions.
 */
class m261101_100000_create_compliance_permissions extends Migration
{
    const PERMISSIONS = [
        'create-standard' => 'Create standards',
        'update-standard' => 'Update standards',
        'delete-standard' => 'Delete standards',
        'clone-standard' => 'Clone a standard for a new assessment period',
        'create-clause' => 'Create clauses',
        'update-clause' => 'Update clauses',
        'delete-clause' => 'Delete clauses',
        'create-sub-clause' => 'Create sub-clauses / controls',
        'update-sub-clause' => 'Update sub-clauses / controls',
        'delete-sub-clause' => 'Delete sub-clauses / controls',
        'create-requirement' => 'Create requirements',
        'update-requirement' => 'Assess and update requirements',
        'delete-requirement' => 'Delete requirements',
        'view-tenants' => 'View tenants',
        'manage-tenants' => 'Create, update and delete tenants',
        'manage-rbac' => 'Manage roles, permissions and user assignments',
        'manage-users' => 'View and manage system users',
    ];

    const OFFICER_PERMISSIONS = ['create-requirement', 'update-requirement'];

    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $admin = $this->ensureRole('compliance-admin');
        $officer = $this->ensureRole('compliance-officer');

        foreach (self::PERMISSIONS as $name => $description) {
            $permission = $auth->getPermission($name);
            if ($permission === null) {
                $permission = $auth->createPermission($name);
                $permission->description = $description;
                $auth->add($permission);
            }
            if (!$auth->hasChild($admin, $permission)) {
                $auth->addChild($admin, $permission);
            }
            if (in_array($name, self::OFFICER_PERMISSIONS, true) && !$auth->hasChild($officer, $permission)) {
                $auth->addChild($officer, $permission);
            }
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        foreach (array_keys(self::PERMISSIONS) as $name) {
            if ($permission = $auth->getPermission($name)) {
                $auth->remove($permission);
            }
        }
    }

    private function ensureRole($name)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);
        if ($role === null) {
            $role = $auth->createRole($name);
            $auth->add($role);
        }
        return $role;
    }
}

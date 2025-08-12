<?php
namespace frontend\controllers;

use app\models\UserRoleForm;
use Yii;
use app\models\User;
use yii\web\Controller;
use frontend\models\RoleForm;
use app\models\PermissionForm;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

class RbacController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Only admins can access
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $dataProvider = new ArrayDataProvider([
            'allModels' => $auth->getRoles(),
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }


    public function actionCreateRole()
    {
        $model = new RoleForm(['scenario' => 'create']);
        $auth = Yii::$app->authManager;
        $allPermissions = $auth->getPermissions();

        if ($model->load(Yii::$app->request->post())) {
            // Validate and process
            if ($model->validate()) {
                // Create role
                $role = $auth->createRole($model->role_name);
                if ($auth->add($role)) {
                    // Handle permissions (now always an array)
                    foreach ((array) $model->permissions as $permName) {
                        if ($perm = $auth->getPermission($permName)) {
                            $auth->addChild($role, $perm);
                        }
                    }
                    Yii::$app->session->setFlash('success', 'Role created');
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('create-role', [
            'model' => $model,
            'permissions' => $allPermissions
        ]);
    }

    public function actionUpdateRole($name)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);

        $model = new RoleForm(['scenario' => 'update']);
        $model->role_name = $role->name;
        $model->originalName = $role->name;
        $model->permissions = array_keys($auth->getPermissionsByRole($name));

        $allPermissions = $auth->getPermissions();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Handle role rename
                if ($model->role_name !== $model->originalName) {
                    $newRole = $auth->createRole($model->role_name);
                    $auth->add($newRole);

                    // Transfer children
                    foreach ($auth->getChildren($model->originalName) as $child) {
                        $auth->addChild($newRole, $child);
                    }

                    // Transfer assignments
                    foreach ($auth->getUserIdsByRole($model->originalName) as $userId) {
                        $auth->assign($newRole, $userId);
                    }

                    // Remove old role
                    $auth->remove($role);
                    $role = $newRole;
                }

                // Update permissions
                $auth->removeChildren($role);
                foreach ((array) $model->permissions as $permName) {
                    if ($perm = $auth->getPermission($permName)) {
                        $auth->addChild($role, $perm);
                    }
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Role updated successfully');
                return $this->redirect(['index']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error updating role: ' . $e->getMessage());
            }
        }

        return $this->render('update-role', [
            'model' => $model,
            'permissions' => $allPermissions,
            'isUpdate' => true
        ]);
    }

    public function actionDeleteRole($name)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);

        if (!$role) {
            Yii::$app->session->setFlash('error', "Role '$name' not found");
            return $this->redirect(['index']);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Remove all role assignments first
            $auth->revokeAll($role->name);

            // Remove role hierarchy relationships
            $auth->removeChildren($role);

            // Finally delete the role itself
            if ($auth->remove($role)) {
                $transaction->commit();
                Yii::$app->session->setFlash(
                    'success',
                    "Role '$name' deleted successfully. " .
                    "All associated permissions and user assignments were removed."
                );
            } else {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', "Failed to delete role '$name'");
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash(
                'error',
                "Error deleting role '$name': " . $e->getMessage()
            );
        }

        return $this->redirect(['index']);
    }

    /**
     * List users with their roles
     * @return string
     */
    public function actionUserRoles()
    {
        $auth = Yii::$app->authManager;
        $users = User::find()->all(); // Assuming you have a User model
        $allRoles = $auth->getRoles();

        return $this->render('assignment/user-roles', [
            'users' => $users,
            'allRoles' => $allRoles
        ]);
    }

    /**
     * Update user roles
     * @param mixed $userId
     * @return string|Yii\web\Response
     */
    public function actionAssignRole($userId)
    {
        $auth = Yii::$app->authManager;
        $user = User::findOne($userId); // Assuming User model exists
        $model = new UserRoleForm();
        $model->userId = $userId;

        // Load existing roles
        $model->roles = array_keys($auth->getRolesByUser($userId));

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Remove existing roles
                $auth->revokeAll($userId);

                // Assign new roles
                if (is_array($model->roles)) {
                    foreach ($model->roles as $roleName) {
                        if ($role = $auth->getRole($roleName)) {
                            $auth->assign($role, $userId);
                        }
                    }
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Roles updated successfully');
                return $this->redirect(['assign-role', 'userId' => $userId]);
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error updating roles: ' . $e->getMessage());
            }
        }

        $allRoles = $auth->getRoles();

        return $this->render('assignment/assign-role', [
            'model' => $model,
            'user' => $user,
            'allRoles' => $allRoles
        ]);
    }



    // Permission Management Actions
    public function actionPermissions()
    {
        $auth = Yii::$app->authManager;
        $allPermissions = $auth->getPermissions();

        // Pre-process permissions to find parents
        $processed = [];
        foreach ($allPermissions as $perm) {
            $processed[] = [
                'object' => $perm,
                'parents' => $this->getPermissionParents($perm->name)
            ];
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $processed,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('permissions/index', [
            'dataProvider' => $dataProvider,
            'allPermissions' => $allPermissions
        ]);
    }

    public function actionCreatePermission()
    {
        $model = new PermissionForm(['scenario' => 'create']);
        $auth = Yii::$app->authManager;
        $existingPermissions = $auth->getPermissions();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $permission = $auth->createPermission($model->name);
                $permission->description = $model->description;
                $auth->add($permission);

                if ($model->parent) {
                    $parent = $auth->getPermission($model->parent);
                    $auth->addChild($parent, $permission);
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Permission created');
                return $this->redirect(['permissions']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
            }
        }

        return $this->render('permissions/create', [
            'model' => $model,
            'existingPermissions' => $existingPermissions
        ]);
    }

    private function getPermissionParents($childName)
    {
        $auth = Yii::$app->authManager;
        $parents = [];

        foreach ($auth->getPermissions() as $perm) {
            if ($auth->hasChild($perm, $auth->getPermission($childName))) {
                $parents[] = $this->getPermissionParents($perm->name);
            }
        }

        return $parents;
    }



    public function actionUpdatePermission($name)
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission($name);
        $existingPermissions = $auth->getPermissions();

        $model = new PermissionForm(['scenario' => 'update']);
        $model->name = $permission->name;
        $model->originalName = $permission->name;
        $model->description = $permission->description;

        // Get current parent
        $parents = $this->getPermissionParents($name);
        $model->parent = $parents ? key($parents) : null;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Handle rename
                if ($model->name !== $model->originalName) {
                    $newPermission = $auth->createPermission($model->name);
                    $newPermission->description = $model->description;
                    $auth->add($newPermission);

                    // Transfer hierarchy
                    $this->transferPermissionRelations(
                        $model->originalName,
                        $newPermission->name
                    );

                    $auth->remove($permission);
                    $permission = $newPermission;
                } else {
                    $permission->description = $model->description;
                    $auth->update($model->originalName, $permission);
                }

                // Update parent relationship
                $existingParents = $this->getPermissionParents($permission->name);
                foreach ($existingParents as $parent) {
                    $auth->removeChild($parent, $permission);
                }

                if ($model->parent) {
                    $parent = $auth->getPermission($model->parent);
                    $auth->addChild($parent, $permission);
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Permission updated');
                return $this->redirect(['permissions']);
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
            }
        }

        return $this->render('permissions/update', [
            'model' => $model,
            'existingPermissions' => $existingPermissions
        ]);
    }


    private function transferPermissionRelations($oldName, $newName)
    {
        $auth = Yii::$app->authManager;

        // Transfer children
        foreach ($auth->getChildren($oldName) as $child) {
            $auth->addChild($auth->getPermission($newName), $child);
        }

        // Transfer parent relationships
        foreach ($auth->getParents($oldName) as $parent) {
            $auth->addChild($parent, $auth->getPermission($newName));
        }

        // Transfer role assignments
        foreach ($auth->getRoles() as $role) {
            if ($auth->hasChild($role, $auth->getPermission($oldName))) {
                $auth->addChild($role, $auth->getPermission($newName));
            }
        }
    }

    public function actionDeletePermission($name)
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->getPermission($name);

        if ($permission) {
            $auth->remove($permission);
            Yii::$app->session->setFlash('success', 'Permission deleted');
        } else {
            Yii::$app->session->setFlash('error', 'Permission not found');
        }

        return $this->redirect(['permissions']);
    }


    public function actionManageHierarchy($name)
    {
        $auth = Yii::$app->authManager;
        $item = $auth->getRole($name) ?? $auth->getPermission($name);
        $request = Yii::$app->request;

        if ($request->isPost) {
            $selectedChildren = $request->post('children', []);

            // Remove existing children
            $auth->removeChildren($item);

            // Add new children
            foreach ($selectedChildren as $childName) {
                $child = $auth->getRole($childName) ?? $auth->getPermission($childName);
                $auth->addChild($item, $child);
            }

            return $this->redirect(['view', 'name' => $name]);
        }

        $allItems = $auth->getPermissions();
        $currentChildren = $auth->getChildren($name);

        return $this->render('manage-hierarchy', [
            'item' => $item,
            'allItems' => $allItems,
            'currentChildren' => $currentChildren
        ]);
    }


}
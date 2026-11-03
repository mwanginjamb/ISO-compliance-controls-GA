<?php

namespace console\controllers;

use Yii;
use common\models\User;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Manages RBAC role assignments from the command line, e.g. to bootstrap the
 * first administrator: `php yii rbac/assign <username> compliance-admin`
 */
class RbacController extends Controller
{
    /**
     * Assigns a role to a user.
     * @param string $username
     * @param string $role
     */
    public function actionAssign($username, $role = 'compliance-admin')
    {
        $auth = Yii::$app->authManager;

        if (($user = User::findOne(['username' => $username])) === null) {
            $this->stderr("User '$username' not found.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
        if (($roleItem = $auth->getRole($role)) === null) {
            $this->stderr("Role '$role' not found. Run the migrations first.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
        if ($auth->getAssignment($role, $user->id) !== null) {
            $this->stdout("'$username' already has the '$role' role.\n");
            return ExitCode::OK;
        }

        $auth->assign($roleItem, $user->id);
        $this->stdout("Assigned '$role' to '$username'.\n", Console::FG_GREEN);
        return ExitCode::OK;
    }

    /**
     * Lists the users assigned to a role.
     * @param string $role
     */
    public function actionList($role = 'compliance-admin')
    {
        foreach (Yii::$app->authManager->getUserIdsByRole($role) as $id) {
            $user = User::findOne($id);
            $this->stdout(($user ? $user->username : '(deleted)') . " (#$id)\n");
        }
        return ExitCode::OK;
    }
}

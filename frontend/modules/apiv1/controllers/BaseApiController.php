<?php

namespace app\modules\apiv1\controllers;

use Yii;
use common\Library\SessionAuth;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

/**
 * Base for all apiv1 resources.
 *
 * Authentication: the logged-in web session (with CSRF) for the UI, or a bearer
 * token for other clients. To federate later, add an authenticator that
 * validates the identity provider's JWTs to `authMethods`.
 *
 * Authorization: reads need an authenticated user; writes need the
 * `{action}-{permissionEntity}` RBAC permission, e.g. `update-requirement`.
 */
abstract class BaseApiController extends ActiveController
{
    /** @var string RBAC permission suffix for this resource */
    public $permissionEntity;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                SessionAuth::class,
                HttpBearerAuth::class,
            ],
        ];

        return $behaviors;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (in_array($action, ['create', 'update', 'delete'], true)
            && !Yii::$app->user->can($action . '-' . $this->permissionEntity)) {
            throw new ForbiddenHttpException('You are not allowed to ' . $action . ' this ' . $this->permissionEntity . '.');
        }
    }
}

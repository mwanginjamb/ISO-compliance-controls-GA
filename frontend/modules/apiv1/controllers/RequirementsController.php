<?php
namespace app\modules\apiv1\controllers;

use app\models\Requirements;
use yii\filters\RateLimiter;
use yii\rest\ActiveController;

class RequirementsController extends ActiveController
{
    public $modelClass = Requirements::class;

    // bring on behaviours

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Authenticate using a bearer token
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\HttpBearerAuth::class,
        ];

        // Add the Rate Limiting Behavior
        $behaviors['rateLimit'] = [
            'class' => RateLimiter::class,
            'enableRateLimitHeaders' => true
        ];
        return $behaviors;
    }

}
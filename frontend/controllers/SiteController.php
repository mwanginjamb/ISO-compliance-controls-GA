<?php

namespace frontend\controllers;

use Yii;
use app\models\User;
use yii\helpers\VarDumper;
use yii\web\Controller;
use app\models\Standards;
use yii\filters\VerbFilter;
use common\models\LoginForm;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\VerifyEmailForm;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use frontend\models\ResetPasswordForm;
use yii\base\InvalidArgumentException;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResendVerificationEmailForm;
use common\models\User as IdentityUser;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'error',
                            'captcha',
                            'about',
                            'contact',
                            'login',
                            'request-password-reset',
                            'reset-password',
                            'verify-email',
                            'resend-verification-email'
                        ],
                        'allow' => true, // everyone
                    ],
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'], // Guest Access
                    ],
                    [
                        'actions' => ['logout', 'index', 'generate-token'],
                        'allow' => true,
                        'roles' => ['@'], // Authenticated
                    ],
                    [
                        'actions' => ['users', 'delete-user'],
                        'allow' => true,
                        'permissions' => ['manage-users'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'generate-token' => ['post'],
                    'delete-user' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $standards = Standards::find()->all();
        return $this->render('index', ['standards' => $standards]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $this->layout = 'guest';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $this->layout = 'guest';
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
            'tenants' => ArrayHelper::map(\app\models\Tenants::find()->all(), 'id', 'name'),
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->verifyEmail()) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model
        ]);
    }


    // Get User Inventory

    public function actionUsers()
    {
        $users = User::find()->select(['id', 'username', 'email', 'created_at', 'status', 'access_token_expires_at'])->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('users', [
            'users' => $users
        ]);
    }

    /**
     * Issues a new API token for the current user. The token is shown once
     * and only its hash is stored.
     */
    public function actionGenerateToken()
    {
        /** @var IdentityUser $user */
        $user = Yii::$app->user->identity;
        $token = $user->generateAccessToken();
        $user->save(false, ['access_token', 'access_token_expires_at']);

        Yii::$app->session->setFlash(
            'success',
            'Your new API token (copy it now, it will not be shown again): ' . $token
        );
        return $this->redirect(Yii::$app->user->can('manage-users') ? ['users'] : ['index']);
    }

    /**
     * Soft-deletes a user: marks them as deleted and revokes their API token,
     * which blocks both web login and API access while keeping related records intact.
     */
    public function actionDeleteUser($id)
    {
        $user = IdentityUser::findOne((int) $id);
        if ($user === null) {
            throw new NotFoundHttpException('The requested user does not exist.');
        }

        if ((int) $user->id === (int) Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'You cannot delete your own account.');
            return $this->redirect(['users']);
        }

        $user->status = IdentityUser::STATUS_DELETED;
        $user->access_token = null;
        $user->access_token_expires_at = null;

        if ($user->save(false, ['status', 'access_token', 'access_token_expires_at'])) {
            Yii::$app->session->setFlash('success', 'User "' . $user->username . '" has been deleted.');
        } else {
            Yii::$app->session->setFlash('error', 'Unable to delete the user.');
        }

        return $this->redirect(['users']);
    }
}

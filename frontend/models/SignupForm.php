<?php

namespace frontend\models;

use app\models\Tenants;
use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;

    public $tenant_id;

    public $confirm_password;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],
            ['email', 'match', 'pattern' => '/^[^@]+@kemri\.go\.ke$/i', 'message' => 'Only kemri.go.ke email addresses are allowed.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
            ['confirm_password', 'compare', 'compareAttribute' => 'password'],


            ['tenant_id', 'required', 'message' => 'Please select a tenant.'],
            ['tenant_id', 'exist', 'skipOnError' => true, 'targetClass' => Tenants::class, 'targetAttribute' => ['tenant_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            //'username' => 'Username',
            //'email' => 'Email',
            //'password' => 'Password',
            //'confirm_password' => 'Confirm Password',
            'tenant_id' => 'Business Unit',
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->tenant_id = $this->tenant_id; //from tenants dropdown

        return $user->save() && $this->sendEmail($user);
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}

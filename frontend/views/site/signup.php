<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to signup:</p>

    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

            <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'email', ['inputOptions' => ['type' => 'email', 'placeholder' => 'Your KEMRI Email']]) ?>

            <?= $form->field($model, 'tenant_id')->dropDownList($tenants, ['prompt' => 'Select Business Unit']) ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'confirm_password')->passwordInput() ?>

            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-user-plus"></i> Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                <?= Html::a('<i class="fa fa-arrow-left"></i> Back To Login', ['site/login'], ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
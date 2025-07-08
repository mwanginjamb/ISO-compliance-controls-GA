<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Requirements $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="requirements-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'evidence_path')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gaps')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'actions_required')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sub_clause_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

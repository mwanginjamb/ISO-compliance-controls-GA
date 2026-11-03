<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\CloneStandardForm $model */
/** @var app\models\Standards $source */

$this->title = Yii::t('app', 'Clone Gap Analysis');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Standards'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $source->standard, 'url' => ['view', 'id' => $source->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="standards-clone card mt-3">
    <div class="card-header">
        <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="card-body">
        <p>
            Creates a new gap analysis from
            <strong><?= Html::encode($source->standard) ?></strong><?= $source->period ? ' (' . Html::encode($source->period) . ')' : '' ?>,
            copying all its clauses, controls and requirements.
            Unless you choose to carry them over, every requirement starts as
            <em>Not Implemented</em> with no evidence, gaps, actions, assignee or timeline.
        </p>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'standard')->textInput(['maxlength' => 250])
            ->hint('Must be unique, e.g. "2026/2027 Gap Analysis - ISO 27001:2022".') ?>

        <?= $form->field($model, 'period')->textInput(['maxlength' => 50, 'placeholder' => 'e.g. 2026/2027']) ?>

        <?= $form->field($model, 'carryOverResults')->checkbox()
            ->hint('Use the previous period as a baseline to update, instead of starting a fresh assessment.') ?>

        <?= $form->field($model, 'carryOverAssignments')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Clone'), ['class' => 'btn btn-success']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['view', 'id' => $source->id], ['class' => 'btn btn-outline-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

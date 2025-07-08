<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SubClause $model */

$this->title = Yii::t('app', 'Create Sub Clause');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sub Clauses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-clause-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

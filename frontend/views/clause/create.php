<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Clause $model */

$this->title = Yii::t('app', 'Create Clause');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clauses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clause-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

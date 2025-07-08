<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Requirements $model */

$this->title = Yii::t('app', 'Create Requirements');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Requirements'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="requirements-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

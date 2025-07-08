<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Standards $model */

$this->title = Yii::t('app', 'Create Standards');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Standards'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="standards-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

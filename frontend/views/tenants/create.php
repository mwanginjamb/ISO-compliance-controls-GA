<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Tenants $model */

$this->title = Yii::t('app', 'Create Tenants');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tenants'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tenants-create card mt-3">

    <div class="card-header">
        <h1 class="card-title"><?= Html::encode($this->title) ?></h1>
    </div>
    <div class="card-body">

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
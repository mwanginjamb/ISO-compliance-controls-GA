<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Tenants $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tenants'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tenants-view">


    <div class="card card-info">
        <div class="card-header">
            <h1 class="card-title"><?= Html::encode($this->title) ?></h1>
            <div class="card-tools">
                <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>

            </div>
        </div>
        <div class="card-body"></div>


        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'name',
                'database_name',
                'unique_identifier',
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>

    </div>

</div>
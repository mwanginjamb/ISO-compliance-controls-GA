<?php

use app\models\Tenants;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\TenantsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Tenants');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tenants-index">

    <div class="card mt-3">
        <div class="card-header">
            <h1 class="card-title"><?= Html::encode($this->title) ?></h1>
            <div class="card-tools">
                <?= Html::a(Yii::t('app', 'Create Tenants'), ['create'], ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        <div class="card-body">
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'id',
                    'name',
                    'database_name',
                    'unique_identifier',
                    'created_at',
                    //'updated_at',
                    [
                        'class' => ActionColumn::className(),
                        'urlCreator' => function ($action, Tenants $model, $key, $index, $column) {
                                        return Url::toRoute([$action, 'id' => $model->id]);
                                    }
                    ],
                ],
            ]); ?>

        </div>
    </div>





</div>
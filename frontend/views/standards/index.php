<?php

use app\models\Standards;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\StandardsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Standards');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="standards-index">

    <div class="card card-info">
        <div class="card-header">

            <div class="d-flex justify-content-between">
                <div class="card-title align-self-center">
                    <h2 class="justify-content-center"><?= Html::encode($this->title) ?></h2>
                </div>
                <div class="card-tools">
                    <?= Html::a(Yii::t('app', 'Add Standard'), ['create'], ['class' => 'btn btn-success']) ?>
                </div>

            </div>
        </div>
        <div class="card-body">
            <?php Pjax::begin(); ?>
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    'standard',
                    'created_at:datetime',
                    //'updated_at',
                    //'created_by',
                    //'updated_by',
                    [
                        'class' => ActionColumn::className(),
                        'urlCreator' => function ($action, Standards $model, $key, $index, $column) {
                                        return Url::toRoute([$action, 'id' => $model->id]);
                                    }
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>

        </div>
    </div>





</div>
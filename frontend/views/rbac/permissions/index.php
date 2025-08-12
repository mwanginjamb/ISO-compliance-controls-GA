<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $allPermissions yii\rbac\Permission[] */
$this->title = 'RBAC Permissions Management';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= $this->title ?></h3>
        <div class="card-tools">
            <?= Html::a('Create New Permission', ['create-permission'], [
                'class' => 'btn btn-success'
            ]) ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'name',
                    'value' => function ($model) {
                    return $model['object']->name;
                }
                ],
                [
                    'attribute' => 'description',
                    'value' => function ($model) {
                    return $model['object']->description;
                }
                ],
                [
                    'attribute' => 'parents',
                    'value' => function ($model) {
                    return $model['parents'] ? implode(', ', $model['parents']) : '-';
                }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                        return Html::a(
                            'Update',
                            ['update-permission', 'name' => $model['object']->name],
                            ['class' => 'btn btn-xs btn-primary']
                        );
                    },
                        'delete' => function ($url, $model) {
                        return Html::a(
                            'Delete',
                            ['delete-permission', 'name' => $model['object']->name],
                            [
                                'class' => 'btn btn-xs btn-danger',
                                'data' => [
                                    'confirm' => 'Are you sure?',
                                    'method' => 'post',
                                ]
                            ]
                        );
                    }
                    ]
                ],
            ],
        ]) ?>

    </div>
</div>
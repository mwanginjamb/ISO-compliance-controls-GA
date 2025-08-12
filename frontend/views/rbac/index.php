<?php
use yii\bootstrap5\Html;
$this->title = 'RBAC Roles';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= $this->title ?></h3>
        <div class="card-tools">
            <?= yii\helpers\Html::a('Create New Role', ['create-role'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <div class="card-body">
        <?= yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'attribute' => 'name',
                    'value' => function ($model) {
                                return strtoupper($model->name);
                            }
                ],
                // 'description',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                                    return Html::a(
                                        '<i class="fas fa-edit"></i>',
                                        ['update-role', 'name' => $model->name],
                                        [
                                            'class' => 'btn btn-primary btn-sm',
                                            'title' => 'Update Role Permissions'
                                        ]
                                    );
                                },
                        'delete' => function ($url, $model) {
                                    return Html::a(
                                        '<i class="fas fa-trash"></i>',
                                        ['delete-role', 'name' => $model->name],
                                        [
                                            'class' => 'btn btn-danger btn-sm',
                                            'data' => [
                                                'confirm' => 'Are you sure you want to delete this role? ' .
                                                    'This will remove all associated permissions and user assignments!',
                                                'method' => 'post',
                                            ],
                                            'title' => 'Delete Role'
                                        ]
                                    );
                                }
                    ]
                ],
            ],
        ]) ?>

    </div>
</div>
<h1>Permissions Management</h1>

<?= yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'name',
        'description',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
        ],
    ],
]) ?>

<?= yii\helpers\Html::a('Create New Permission', ['create-permission'], ['class' => 'btn btn-success']) ?>
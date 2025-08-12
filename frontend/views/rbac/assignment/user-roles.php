<?php
$this->title = 'User Roles Assignment';
$auth = Yii::$app->authManager;
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= $this->title ?></h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="table">
            <thead>
                <tr>
                    <td class="fw-bold">User</td>
                    <td class="fw-bold">Assignment Action</td>
                    <td class="fw-bold">Assigned Roles</td>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user->username ?></td>
                        <td><?= yii\helpers\Html::a(
                            'Assign Roles',
                            ['assign-role', 'userId' => $user->id],
                            ['class' => 'btn btn-xs btn-primary']
                        ) ?></td>
                        <td><?= implode(', ', array_keys($auth->getRolesByUser($user->id))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<?php

$script = <<<JS
    $('#table').DataTable();
JS;

$this->registerJs($script);
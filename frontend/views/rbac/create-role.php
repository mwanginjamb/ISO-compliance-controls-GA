<?php
$this->title = 'Create Role';
use yii\helpers\Html;

/* @var $model backend\models\RoleForm */
/* @var $permissions yii\rbac\Permission[] */
?>

<div class="role-create">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="card-body">
            <?= $this->render('_form_role', [
                'model' => $model,
                'permissions' => $permissions,
                'isUpdate' => false
            ]) ?>

        </div>
    </div>

</div>
<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PermissionForm */
/* @var $existingPermissions yii\rbac\Permission[] */

$this->title = 'Create New Permission';
?>
<div class="permission-create">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="card-body">
            <?= $this->render('_form', [
                'model' => $model,
                'existingPermissions' => $existingPermissions,
                'isUpdate' => false
            ]) ?>

        </div>
    </div>

</div>
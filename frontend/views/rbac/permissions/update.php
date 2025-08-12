<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PermissionForm */
/* @var $existingPermissions yii\rbac\Permission[] */

$this->title = 'Update Permission: ' . $model->name;
?>
<div class="permission-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'existingPermissions' => $existingPermissions,
        'isUpdate' => true
    ]) ?>
</div>
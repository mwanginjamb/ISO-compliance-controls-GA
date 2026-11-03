<?php
use yii\helpers\Html;

$form = yii\widgets\ActiveForm::begin() ?>

<?= $form->field($model, 'role_name')->textInput() ?>

<h3>Assign Permissions/Roles</h3>
<div class="permission-tree">
    <?php foreach ($allItems as $item): ?>
        <div class="form-check">
            <?= yii\helpers\Html::checkbox(
                "children[]",
                false,
                [
                    'value' => $item->name,
                    'class' => 'form-check-input',
                    'id' => 'perm-' . $item->name
                ]
            ) ?>
            <label class="form-check-label" for="perm-<?= Html::encode($item->name) ?>">
                <?= Html::encode($item->name) ?>
                <span class="text-muted">(<?= Html::encode($item->description) ?>)</span>
            </label>
        </div>
    <?php endforeach; ?>
</div>

<?= yii\helpers\Html::submitButton('Create Role', ['class' => 'btn btn-primary']) ?>

<?php yii\widgets\ActiveForm::end() ?>
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model backend\models\RoleForm */
/* @var $permissions yii\rbac\Permission[] */
/* @var $isUpdate bool */
?>

<?php $form = ActiveForm::begin([
    'id' => 'role-form',
    // 'enableAjaxValidation' => true,
]); ?>

<?= $form->field($model, 'role_name')->textInput([
    'maxlength' => true,
    'autofocus' => !$isUpdate,
    'readonly' => $isUpdate && $model->role_name === $model->originalName
]) ?>

<div class="permissions px-3">
    <?= $form->field($model, 'permissions')->checkboxList(
        \yii\helpers\ArrayHelper::map($permissions, 'name', 'description'),
        [
            'item' => function ($index, $label, $name, $checked, $value) {
                    return Html::checkbox($name, $checked, [
                        'value' => $value,
                        'label' => '<div class="badge badge-info ">' . $label . '</div>',
                        'labelOptions' => ['class' => 'mx-3 '],
                        'class' => 'form-check-input'
                    ]);
                }
        ]
    ) ?>

</div>

<?php if ($isUpdate): ?>
    <?= $form->field($model, 'originalName')->hiddenInput()->label(false) ?>
<?php endif; ?>

<div class="form-group">
    <?= Html::submitButton($isUpdate ? 'Update Role' : 'Create Role', [
        'class' => 'btn btn-primary'
    ]) ?>
    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php

$style = <<<CSS
#roleform-permissions {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin: 1rem 0;
}

#roleform-permissions label{ 
 align-items: center;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 4px;
    transition: background-color 0.2s;
    margin: 0; /* Remove default margins */
}
CSS;

$this->registerCss($style);
?>
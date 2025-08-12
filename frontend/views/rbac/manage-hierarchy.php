<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $item yii\rbac\Role|yii\rbac\Permission */
/* @var $allItems yii\rbac\Permission[]|yii\rbac\Role[] */
/* @var $currentChildren array */
?>

<div class="hierarchy-management">
    <h1>
        Manage Hierarchy: <?= Html::encode($item->name) ?>
        <small class="text-muted">(<?= Html::encode($item->description) ?>)</small>
    </h1>

    <?php $form = ActiveForm::begin() ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Available Items</h3>
                </div>
                <div class="card-body hierarchy-items">
                    <div class="roles-section">
                        <h4>Roles</h4>
                        <?php foreach ($allItems as $child): ?>
                            <?php if ($child instanceof yii\rbac\Role && $child->name !== $item->name): ?>
                                <div class="form-check">
                                    <?= Html::checkbox(
                                        'children[]',
                                        array_key_exists($child->name, $currentChildren),
                                        [
                                            'value' => $child->name,
                                            'id' => 'child_' . $child->name,
                                            'class' => 'form-check-input'
                                        ]
                                    ) ?>
                                    <label class="form-check-label" for="child_<?= $child->name ?>">
                                        <i class="fas fa-users-cog text-primary"></i>
                                        <?= Html::encode($child->name) ?>
                                        <?php if ($child->description): ?>
                                            <br>
                                            <small class="text-muted">
                                                <?= Html::encode($child->description) ?>
                                            </small>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <div class="permissions-section">
                        <h4>Permissions</h4>
                        <?php foreach ($allItems as $child): ?>
                            <?php if ($child instanceof yii\rbac\Permission && $child->name !== $item->name): ?>
                                <div class="form-check">
                                    <?= Html::checkbox(
                                        'children[]',
                                        array_key_exists($child->name, $currentChildren),
                                        [
                                            'value' => $child->name,
                                            'id' => 'child_' . $child->name,
                                            'class' => 'form-check-input'
                                        ]
                                    ) ?>
                                    <label class="form-check-label" for="child_<?= $child->name ?>">
                                        <i class="fas fa-key text-success"></i>
                                        <?= Html::encode($child->name) ?>
                                        <?php if ($child->description): ?>
                                            <br>
                                            <small class="text-muted">
                                                <?= Html::encode($child->description) ?>
                                            </small>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Current Hierarchy</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($currentChildren)): ?>
                        <div class="current-hierarchy">
                            <?php foreach ($currentChildren as $child): ?>
                                <div
                                    class="alert alert-sm mb-2 <?= $child instanceof yii\rbac\Role ? 'alert-primary' : 'alert-success' ?>">
                                    <i class="fas fa-fw <?= $child instanceof yii\rbac\Role ? 'fa-users-cog' : 'fa-key' ?>"></i>
                                    <strong><?= Html::encode($child->name) ?></strong>
                                    <?php if ($child->description): ?>
                                        <br>
                                        <span class="text-muted"><?= Html::encode($child->description) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            No child items assigned yet
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group mt-4">
        <?= Html::submitButton('Save Hierarchy', ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end() ?>
</div>

<style>
    .hierarchy-items {
        max-height: 70vh;
        overflow-y: auto;
    }

    .current-hierarchy {
        max-height: 60vh;
        overflow-y: auto;
    }

    .form-check {
        margin-bottom: 0.5rem;
        padding: 0.25rem;
        border-radius: 4px;
    }

    .form-check:hover {
        background-color: #f8f9fa;
    }
</style>
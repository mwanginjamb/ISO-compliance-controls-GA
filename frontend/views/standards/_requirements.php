<?php
use yii\helpers\Url;
use yii\bootstrap5\Html;
?>

<tr class="child">
    <td colspan="4">
        <div class="card">
            <div class="card-header">
                <div class="headaction">
                    <h3 class="card-title">Requirements (<?= count($sc->requirements) ?>)</h3>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-capitalize text-bold text-info">Requirement Description</th>
                            <th class="text-capitalize text-bold text-info">Status</th>
                            <th class="text-capitalize text-bold text-info">Evidence</th>
                            <th class="text-capitalize text-bold text-info">Gaps</th>
                            <th class="text-capitalize text-bold text-info">Actions Required</th>
                            <th>Assignment</th>
                            <th class="text-bold"><?= Html::a('Add a Requirement', Url::home(true) . 'apiv1/requirements', [
                                'class' => 'btn btn-sm btn-info add',
                                'data-sub_clause_id' => $sc->id,
                                'data-description' => 'requirement - ' . date('Y-m-d H:i:s'),
                                'data-template' => 1,
                                'data-status' => 0,
                                'data-endpoint' => Url::home(true) . 'apiv1/requirements',
                                // 'data-reload' => 1
                            ]) ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- row template -->
                        <tr class="templateRow" style="display: none">
                            <td data-name="description"></td>
                            <td data-name="status">5</td>
                            <td data-name="evidence_path"></td>
                            <td data-name="gaps"></td>
                            <td data-name="Assignee"></td>
                            <td data-name="actions_required"></td>
                            <td>
                                <?= Html::a('<i class="bi bi-trash"></i>', '#', ['class' => 'btn btn-danger btn-sm delete']) ?>
                            </td>
                        </tr>
                        <!-- /row template -->
                        <?php foreach ($sc->requirements as $r):
                            $endpoint = Url::home(true) . 'apiv1/requirements/' . $r->id;
                            ?>

                            <tr>
                                <td data-key="<?= $r->id ?>" data-name="description" data-service="<?= $endpoint ?>"
                                    ondblclick="addTextarea(this)"><?= $r->description ?></td>
                                <td data-key="<?= $r->id ?>" data-name="status" data-service="<?= $endpoint ?>"
                                    ondblclick="addDropDown(this,'status')" data-reload="1"><?= $r->status ?></td>
                                <td data-key="<?= $r->id ?>" data-name="evidence_path" data-service="<?= $endpoint ?>"
                                    ondblclick="addTextarea(this)"><?= $r->evidence_path ?></td>
                                <td data-key="<?= $r->id ?>" data-name="gaps" data-service="<?= $endpoint ?>"
                                    ondblclick="addTextarea(this)"><?= $r->gaps ?></td>
                                <td data-key="<?= $r->id ?>" data-name="actions_required" data-service="<?= $endpoint ?>"
                                    ondblclick="addTextarea(this)"><?= $r->actions_required ?></td>
                                <td data-key="<?= $r->id ?>" data-name="assignee" data-service="<?= $endpoint ?>"
                                    ondblclick="addDropDown(this,'assignees')">Assignee
                                </td>
                                <td>
                                    <?= Html::a('<i class="bi bi-trash"></i>', $endpoint, ['
                                    class' => 'btn btn-danger btn-sm delete',
                                        'data-service' => $endpoint,
                                        'data-key' => $r->id
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </td>
</tr>
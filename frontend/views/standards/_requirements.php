<?php
use yii\helpers\Url;
use yii\bootstrap5\Html;
?>

<tr class="child">
    <td colspan="3">
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
                            <th class="text-capitalize text-bold">Requirement Description</th>
                            <th class="text-capitalize text-bold">Status</th>
                            <th class="text-capitalize text-bold">Evidence</th>
                            <th class="text-capitalize text-bold">Gaps</th>
                            <th class="text-capitalize text-bold">Actions Required</th>
                            <th class="text-bold"><?= Html::a('Add a Requirement', Url::home(true) . 'apiv1/requirements', [
                                'class' => 'btn btn-sm btn-info add',
                                'data-sub_clause_id' => $sc->id,
                                'data-description' => 'requirement - ' . date('Y-m-d H:i:s'),
                                'data-template' => 1,
                                'data-status' => 0,
                                'data-endpoint' => Url::home(true) . 'apiv1/requirements',
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
                            <td data-name="actions_required"></td>
                            <td>
                                <?= Html::a('<i class="bi bi-trash"></i>', '#', ['class' => 'btn btn-danger btn-sm delete']) ?>
                            </td>
                        </tr>
                        <!-- /row template -->
                        <?php foreach ($sc->requirements as $r):
                            $endpoint = Url::home(true) . 'apiv1/clauses/' . $r->id;
                            ?>

                            <tr>
                                <td><?= $r->description ?></td>
                                <td><?= $r->status ?></td>
                                <td><?= $r->evidence_path ?></td>
                                <td><?= $r->gaps ?></td>
                                <td><?= $r->actions_required ?></td>
                                <td>
                                    <?= Html::a('<i class="bi bi-trash"></i>', $endpoint, ['class' => 'btn btn-danger btn-sm delete']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </td>
</tr>
<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Standards $model */

$this->title = 'Gap Analysis for' . $model->standard;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Standards'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="standards-view">

    <h1 class="lead text-center"><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">

                <div class="card-title align-self-center">Clauses</div>
                <div class="card-tools">
                    <div class="btn-group">
                        <?= Html::a('Update standard', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ],
                        ]) ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- show Clauses -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-capitalize text-bold text-center">Clause</th>
                            <th class="text-bold">actions
                                <div class="float-right">
                                    <?= Html::a('Add a clause', Url::home(true) . 'apiv1/clauses', [
                                        'class' => 'btn btn-warning add',
                                        'data-standard_id' => $model->id,
                                        'data-title' => 'clause - ' . date('Y-m-d H:i:s'),
                                        'data-template' => 1,
                                        'data-endpoint' => Url::home(true) . 'apiv1/clauses',
                                    ]) ?>
                                </div>

                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--  template -->
                        <tr class="templateRow parent" style="display: none">
                            <td data-name="id">1</td>
                            <td data-name="title">5</td>
                            <td>
                                <?= Html::a('<i class="bi bi-trash"></i>', '#', ['class' => 'btn btn-danger btn-sm delete']) ?>
                            </td>
                        </tr>
                        <!-- /row template -->
                        <?php
                        $count = 0;
                        foreach ($model->clauses as $c):
                            $endpoint = Url::home(true) . 'apiv1/clauses/' . $c->id;
                            $count++;
                            ?>
                            <tr class="parent">
                                <td><?= $count ?></td>
                                <td colspan="2" data-key="<?= $c->id ?>" data-name="title" data-service="<?= $endpoint ?>"
                                    ondblclick="addInput(this)">
                                    <?= $c->title ?>
                                </td>

                            </tr>
                            <tr class="child">
                                <td colspan="3">
                                    <!-- subclauses -->
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <td class="text-capitalize text-center text-bold">#</td>
                                                <td class="text-capitalize text-center text-bold">Controls</td>
                                                <td class=" text-center text-bold">
                                                    <?= Html::a('Add a Control', Url::home(true) . 'apiv1/sub-clauses', [
                                                        'class' => 'btn btn-sm btn-primary add',
                                                        'title' => 'Add a Sub-Clause',
                                                        'data-number' => Yii::$app->security->generateRandomString(3),
                                                        'data-sub_clause' => 'sub clause - ' . date('Y-m-d H:i:s'),
                                                        'data-template' => 1,
                                                        'data-clause_id' => $c->id,
                                                        'data-endpoint' => Url::home(true) . 'apiv1/sub-clauses',
                                                    ]) ?>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- hidden template -->
                                            <tr class="templateRow" style="display: none">
                                                <td data-name="number"></td>
                                                <td data-name="sub_clause"></td>
                                                <td>
                                                    <?= Html::a('<i class="bi bi-trash"></i>', '#', ['class' => 'btn btn-danger btn-sm delete']) ?>
                                                </td>
                                            </tr>
                                            <!-- /row template -->
                                            <?php foreach ($c->subClauses as $sc):
                                                $endpoint = Url::home(true) . 'apiv1/clauses/' . $c->id;
                                                ?>
                                                <tr class="parent">
                                                    <td><?= $sc->number ?></td>
                                                    <td><?= $sc->sub_clause ?></td>
                                                    <td>
                                                        <?= Html::a('<i class="bi bi-trash"></i>', $endpoint, ['class' => 'btn btn-danger btn-sm delete']) ?>
                                                    </td>
                                                </tr>

                                                <!-- Requirements -->
                                                <?= $this->render('_requirements', ['sc' => $sc]) ?>
                                                <!--/ requirements -->
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <!-- / sub clauses -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>



</div>
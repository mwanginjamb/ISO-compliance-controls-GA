<?php

use yii\bootstrap5\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = Yii::$app->name;
?>
<div class="site-index">

    <h1 class="text-center">Standards for Gap Analysis</h1>

    <div class="body-content">

        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="text-capitalize">Standard</th>
                        <th class="text-capitalize">actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 0;
                    foreach ($standards as $c):
                        $count++;
                        ?>
                        <tr>
                            <td data-name="count"><?= $count ?></td>
                            <td data-name="standard"><?= $c->standard ?></td>
                            <td>
                                <?= Html::a('<i class="bi bi-eye mx-sm-1"></i> View Clauses', Url::toRoute(['standards/view', 'id' => $c->id]), [
                                    'class' => 'btn btn-sm btn-primary',
                                    'data' => [
                                        'method' => 'get',
                                        'params' => [
                                            'id' => $c->id
                                        ]
                                    ]
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>


        </div>

    </div>
</div>
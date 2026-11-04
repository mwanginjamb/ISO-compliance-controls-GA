<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */

$this->title = 'App Users';
$canManageUsers = Yii::$app->user->can('manage-users');
?>
<div class="site-index">

    <div class="body-content">
        <div class="card card-info">
            <div class="card-header">
                <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
                <div class="card-tools">
                    <?= Html::a('<i class="fas fa-key mx-1"></i>Generate my API token', ['generate-token'], [
                        'class' => 'btn btn-sm btn-warning',
                        'data' => [
                            'confirm' => 'Generate a new API token? Your current token, if any, will stop working.',
                            'method' => 'post',
                        ]
                    ]) ?>
                </div>
            </div>
            <div class="card-body">

                <!-- dashboard -->
                <?= $this->render('_users') ?>
                <!-- / dashboard -->

                <div class="row">
                    <div class="col my-3 p-2 d-flex justify-content-between ">
                        <div class="title float-left text-bold">Export
                            Actions <i class="fa fa-arrow-alt-circle-right"></i></div>
                        <div id="toolbar"></div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="table">
                        <thead>
                            <tr>
                                <td class="fw-bold">#</td>
                                <td class="fw-bold">Username</td>
                                <td class="fw-bold">Email</td>
                                <td class="fw-bold">Created At</td>
                                <td class="fw-bold">Staff Number</td>
                                <td class="fw-bold">API Token</td>
                                <td class="fw-bold">Status</td>
                                <?php if ($canManageUsers): ?>
                                    <td class="fw-bold">Actions</td>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users && is_array($users)): ?>
                                <?php foreach ($users as $c):

                                    $status = null;
                                    if ($c->status == 10) {
                                        $status = '<div class="badge bg-success">Active</div>';
                                    } else if ($c->status == 9) {
                                        $status = '<div class="badge badge-dark">Inactive</div>';
                                    } else if ($c->status == 0) {
                                        $status = '<div class="badge badge-danger">Deleted</div>';
                                    }
                                    ?>
                                    <tr>
                                        <td><?= Html::encode($c->id) ?></td>
                                        <td><?= Html::encode($c->username) ?></td>
                                        <td><?= Html::encode(strtoupper($c->email)) ?></td>
                                        <td><?= Yii::$app->formatter->asDatetime($c->created_at) ?></td>
                                        <td><?= Html::encode($c->staff_id_number ?? '') ?></td>
                                        <td>
                                            <?php if ($c->access_token_expires_at > time()): ?>
                                                Valid until <?= Yii::$app->formatter->asDate($c->access_token_expires_at) ?>
                                            <?php else: ?>
                                                <span class="text-muted">None</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $status ?></td>
                                        <?php if ($canManageUsers): ?>
                                            <td>
                                                <?php if ($c->status != 0 && $c->id != Yii::$app->user->id): ?>
                                                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete-user', 'id' => $c->id], [
                                                        'class' => 'btn btn-sm btn-outline-danger',
                                                        'title' => 'Delete user',
                                                        'data' => [
                                                            'confirm' => 'Delete user "' . $c->username . '"? They will no longer be able to log in or use the API.',
                                                            'method' => 'post',
                                                        ],
                                                    ]) ?>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>


    </div>
</div>

<?php

$script = <<<JS
    let table = $('#table').DataTable({
        pageLength: 50,
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-secondary btn-sm'
            },
            {
                extend: 'csv',
                className: 'btn btn-dark btn-sm',
                filename: 'users_export_' + new Date().toISOString().slice(0,10),
                exportOptions: {
                    columns: [1, 2, 3, 4, 5] // Exclude action columns (0,6)
                }
            },
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm',
                filename: 'users_export_' + new Date().toISOString().slice(0,10),
                title: 'Filtered System Users Report',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5] // Exclude action columns (0,6)
                }
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger btn-sm',
                filename: 'users_export_' + new Date().toISOString().slice(0,10),
                title: 'Filtered System Users Report',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5] // Exclude action columns (0,6)
                },
                customize: function(doc) {
                    // Customize PDF styling
                    doc.defaultStyle.fontSize = 9;
                    doc.styles.tableHeader.fontSize = 10;
                    doc.styles.tableHeader.fillColor = '#007bff';
                    doc.styles.tableHeader.color = 'white';
                    
                    // Add header
                    doc.content.splice(0, 0, {
                        text: 'Filtered System Users Report',
                        style: 'title',
                        alignment: 'center',
                        margin: [0, 0, 0, 20]
                    });
                    
                    // Add generation date
                    doc.content.splice(1, 0, {
                        text: 'Generated on: ' + new Date().toLocaleString(),
                        style: 'subheader',
                        alignment: 'right',
                        margin: [0, 0, 0, 20]
                    });
                    
                    doc.styles.title = {
                        fontSize: 16,
                        bold: true
                    };
                    
                    doc.styles.subheader = {
                        fontSize: 10,
                        italics: true
                    };
                }
            },
            {
                extend: 'print',
                className: 'btn btn-outline-info bg-light btn-sm',
                title: 'Filtered System Users Report',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5] // Exclude action columns (7, 8, 9)
                },
                customize: function(win) {
                    // Customize print styling
                    $(win.document.body)
                        .css('font-size', '10pt')
                        .prepend(
                            '<div style="text-align:center; margin-bottom: 20px;"><h2>Filtered System Users Report</h2><p>Generated on: ' + new Date().toLocaleString() + '</p></div>'
                        );
                        
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', '9pt');
                }
            }
        ]
    });

    table.buttons().container().appendTo($('.card-tools, #toolbar'));
     // Add some custom styling to the buttons container
     $('.dt-buttons').addClass('float-end');
JS;

$this->registerJs($script);

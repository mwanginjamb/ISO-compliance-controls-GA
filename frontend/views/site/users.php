<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */

$this->title = 'App Users';
?>
<div class="site-index">

    <div class="body-content">
        <div class="card card-info">
            <div class="card-header">
                <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
                <div class="card-tools"></div>
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
                                <td class="fw-bold">Access Token</td>
                                <td class="fw-bold">Status</td>
                                <td class="fw-bold">Token Action</td>
                                <td class="fw-bold">Action</td>
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
                                        <td><?= strtoupper($c->id) ?? '' ?></td>
                                        <td><?= $c->username ?? '' ?></td>
                                        <td><?= strtoupper($c->email) ?? '' ?></td>
                                        <td><?= Yii::$app->formatter->asDatetime($c->created_at) ?? '' ?></td>
                                        <td><?= $c->staff_id_number ?? '' ?></td>
                                        <td><?= $c->access_token ?? '' ?></td>
                                        <td><?= $status ?></td>
                                        <td><?= Html::a('<i class="fas fa-key mx-1"></i>Generate Token', ['generate-token'], [
                                            'class' => 'btn btn-xs btn-warning',
                                            'data' => [
                                                'confirm' => 'Are you sure you want to generate an API token? .',
                                                'method' => 'post',

                                            ]
                                        ]) ?>
                                        </td>
                                        <td><?= Html::a('<i class="fas fa-trash mx-1"></i>Delete', ['site/delete'], [
                                            'class' => 'btn btn-xs btn-danger',
                                            'data' => [
                                                'confirm' => 'Are you sure you want to delete this user?  Note this is a destructive action.',
                                                'method' => 'post',
                                                'params' => [
                                                    'id' => $c->id
                                                ]
                                            ]
                                        ]) ?>
                                        </td>
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

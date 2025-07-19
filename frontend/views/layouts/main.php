<?php
use yii\helpers\Html;
use frontend\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php
    $style = <<<CSS
html, body {
    height: 100%;
    background-color: #f4f6fa;
    font-size: 14px;
    margin: 0;
}
body {
    display: flex;
    flex-direction: column;
}
.content-wrap {
    flex: 1 0 auto;
}
.footer {
    flex-shrink: 0;
    background: #ffffff;
    border-top: 1px solid #dee2e6;
    padding: 10px 20px;
    font-size: 13px;
    color: #6c757d;
    box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.03);
}
.navbar {
    background-color: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.06);
}
.sidebar {
    background-color: #f9fafc;
    min-height: 100vh;
    border-right: 1px solid #e0e0e0;
    padding-top: 1rem;
}
.sidebar h6, .sidebar small {
    font-size: 13px;
    color: #6c757d;
}
.nav-link {
    color: #495057;
    padding: 8px 12px;
}
.nav-link.active {
    background-color: #e3ebfc;
    color: #2c3e50;
    border-radius: 4px;
}
.card {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    margin-bottom: 1rem;
}
.assessment-card {
    border-left: 4px solid #007bff;
    cursor: pointer;
}
.assessment-card:hover {
    background-color: #f0f5ff;
}
.badge-dot {
    height: 10px;
    width: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 5px;
}
.sidebar .btn {
    font-size: 13px;
}
.progress {
    height: 6px;
}
CSS;
    $this->registerCss($style);
    ?>
</head>

<body>
    <?php $this->beginBody() ?>

    <!-- header -->
    <?= $this->render('_header') ?>

    <!-- Main content -->

    <div class="flex-grow-1 d-flex">
        <div class="content-wrap">
            <div class="container-fluid">
                <div class="row">
                    <!-- Sidebar -->
                    <div class="col-md-2 sidebar">
                        <div class="px-3">
                            <h5>ISO 27001:2022</h5>
                            <small class="text-muted">Gap Analysis Portal</small>
                        </div>
                        <hr>
                        <ul class="nav flex-column px-3">
                            <li class="nav-item">
                                <?= Html::a('Dashboard', ['/site/index'], ['class' => 'nav-link active']) ?>
                            </li>
                            <li class="nav-item">
                                <?= Html::a('Clause Details', ['/clause/index'], ['class' => 'nav-link']) ?>
                            </li>
                        </ul>

                        <div class="px-3 mt-4 d-flex justify-content-between align-items-center">
                            <h6 class="text-muted mb-0">Assessments</h6>
                            <button class="btn btn-sm btn-primary">+ New</button>
                        </div>

                        <!-- Sample assessment card -->
                        <div class="card assessment-card mx-3 mt-2 p-2">
                            <small class="font-weight-bold">Initial Gap Analysis 2024</small>
                            <small class="text-muted d-block">Sample Organization</small>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">0% complete</small>
                                <small class="text-primary font-weight-bold">0% compliant</small>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="col-md-10 p-4">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <!-- Footer -->
    <footer class="footer text-center">
        &copy; <?= date('Y') ?> MyCompany — ISO 27001:2022 Gap Analysis Portal
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>
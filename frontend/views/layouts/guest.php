<?php

/** @var \yii\web\View $this */
/** @var string $content */

use yii\helpers\Url;
use yii\bootstrap5\Nav;
use yii\bootstrap5\Html;
use common\widgets\Alert;
use yii\bootstrap5\NavBar;
use frontend\assets\GuestAsset;
use yii\bootstrap5\Breadcrumbs;

GuestAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="<?= Yii::$app->utility->webroot() ?>/images/icons/favicon-32x32.png" rel="shortcut icon"
        type="image/vnd.microsoft.icon" />

    <!-- Analytics -->
    <script defer src="https://analytics.kemri.go.ke/custom" data-website-id="<?= env('UMAMI_REF') ?>"></script>


    <!-- PWA SHIT -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#134474">
    <link rel="apple-touch-icon" href="/images/manifest/96.png" />
    <meta name="apple-mobile-web-app-status-bar" content="#01A54F">
    <link rel="apple-touch-icon" sizes="180x180"
        href="<?= Yii::$app->utility->webroot() ?>/images/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32"
        href="<?= Yii::$app->utility->webroot() ?>/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16"
        href="<?= Yii::$app->utility->webroot() ?>/images/icons/favicon-16x16.png">

    <!-- / PWA SHIT -->
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <?php $this->head() ?>
</head>

<body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <main>
        <div class="container-fluid px-1 px-md-5 px-lg-1 px-xl-5 py-5 mx-auto">
            <div class="card card0 border-0">
                <div class="row d-flex">
                    <div class="col text-center">
                        <!-- <img src="<?= \yii\helpers\Url::to('/images/kemri-logo.png') ?>" class="logo img-fluid"
                            loading="lazy" alt="Client Logo"> -->
                    </div>
                </div>
                <div class="row d-flex justify-content-center text-center py-4">
                    <h2 class="text-muted display-4">Welcome to <?= env('APP_NAME') ?></h2>
                </div>
                <div class="row d-flex">
                    <div class="col-lg-6">
                        <div class="card1 pb-5">

                            <div class="row px-3 justify-content-center mt-4 mb-5 border-line">
                                <?php if (Yii::$app->utility->currentaction('site', 'request-password-reset')): ?>
                                    <div class="d-flex justify-content-center align-items-start">
                                        <img src="<?= Yii::$app->utility->webroot() ?>/images/undraw_forgot-password.svg"
                                            class="img-fluid  w-75 h-75" />
                                    </div>
                                <?php elseif (Yii::$app->utility->currentaction('site', 'resend-verification-email')): ?>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <img src="<?= Yii::$app->utility->webroot() ?>/images/secure-login.svg"
                                            class="img-fluid" />
                                    </div>
                                <?php elseif (Yii::$app->utility->currentaction('site', 'signup')): ?>
                                    <div class="d-flex justify-content-center align-items-start">
                                        <img src="<?= Yii::$app->utility->webroot() ?>/images/signup.svg"
                                            class="img-fluid" />
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex justify-content-center align-items-start">
                                        <img src="<?= Yii::$app->utility->webroot() ?>/images/certificate.svg"
                                            class="img-fluid w-75 h-75">
                                    </div>
                                <?php endif; ?>
                            </div>




                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card2 card border-0 px-4 py-2">
                            <div class="row mb-2 px-3">
                                <!-- <h6 class="mb-0 mr-4 mt-2">Sign in with</h6>
                                <div class="facebook text-center mr-3">
                                    <div class="fa fa-facebook"></div>
                                </div>
                                <div class="twitter text-center mr-3">
                                    <div class="fa fa-twitter"></div>
                                </div>
                                <div class="linkedin text-center mr-3">
                                    <div class="fa fa-linkedin"></div>
                                </div>
                            </div>
                            <div class="row px-3 mb-4">
                                <div class="line"></div>
                                <small class="or text-center">Or</small>
                                <div class="line"></div>
                            </div> -->
                                <?= Alert::widget() ?>
                                <?= $content ?>
                            </div>

                        </div>

                        <!-- Footer -->
                    </div>
                </div>
            </div>
    </main>
    <footer class="footer mt-auto py-3 text-muted fixed-bottom">
        <div class="bg-info py-2 text-light">
            <div class="d-flex justify-content-between">

                <!-- <small class="ml-4 ml-sm-5 mb-2">Copyright &copy; 2019. All rights reserved.</small> -->
                <p class="float-start ml-4 ml-sm-5 mb-2">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?>
                </p>


                <div class="float-end mr-4">
                    <p class=""><?= Yii::powered() ?></p>
                </div>



            </div>
        </div>
    </footer>


    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage();

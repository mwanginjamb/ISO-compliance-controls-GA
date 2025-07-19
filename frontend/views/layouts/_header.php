<?php

use yii\bootstrap5\Html;

?>
<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="https://via.placeholder.com/30" alt="Logo" class="mr-2">
            <strong>MyCompany</strong>
        </a>
        <div class="ml-auto">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileMenu"
                        data-toggle="dropdown">
                        <img src="https://via.placeholder.com/30" class="rounded-circle mr-2" alt="User">
                        <span><?= Yii::$app->user->isGuest ? 'Guest' : Yii::$app->user->identity->username ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileMenu">
                        <a class="dropdown-item" href="#">Profile</a>
                        <a class="dropdown-item" href="#">Settings</a>
                        <div class="dropdown-divider"></div>
                        <?= Html::a('Logout', ['/site/logout'], [
                            'class' => 'dropdown-item text-danger',
                            'data-method' => 'post'
                        ]) ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php
use yii\helpers\Url;
?>
<div class="row">


    <!-- /.col open -->
    <div class="col-lg-4 col-md-3 col-6">

        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= Yii::$app->dashboard->countActiveUsers() ?></h3>
                <p>Active Users</p>
            </div>
            <div class="icon">
                <i class="ion ion-person"></i>
            </div>
            <a href="<?= Url::toRoute(['site/active-users']) ?>" class="small-box-footer">More info <i
                    class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- /.col -->


    <!-- col Supervisor  -->
    <div class="col-lg-4 col-md-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= Yii::$app->dashboard->countInactiveUsers() ?></h3>
                <p>Inactive Users</p>
            </div>
            <div class="icon">
                <i class="ion ion-person"></i>
            </div>
            <a href="<?= Url::toRoute(['site/inactive-users']) ?>" class="small-box-footer">More
                info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>



    <!-- Col Overview -->
    <div class="col-lg-4 col-md-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= Yii::$app->dashboard->countRequireUpdate() ?></h3>
                <p>Require Update (Emp. No.)</p>
            </div>
            <div class="icon">
                <i class="ion ion-person"></i>
            </div>
            <a href="<?= Url::toRoute(['site/require-update']) ?>" class="small-box-footer">More info
                <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>




</div>
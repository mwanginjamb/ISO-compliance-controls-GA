<?php

/**
 * Created by PhpStorm.
 * User: HP ELITEBOOK 840 G5
 * Date: 2/21/2020
 * Time: 12:34 AM
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AdminlteAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'plugins/fontawesome-free/css/all.min.css',
        'https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css',
        'plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css',
        // 'plugins/icheck-bootstrap/icheck-bootstrap.min.css',
        'plugins/jqvmap/jqvmap.min.css',
        // 'plugins/datatables-bs4/css/dataTables.bootstrap4.css',
        // 'https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css',
        'dist/css/adminlte.min.css',
        'plugins/overlayScrollbars/css/OverlayScrollbars.min.css',
        'plugins/daterangepicker/daterangepicker.css',
        'plugins/summernote/summernote-bs4.css',
        'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700',

        // 'https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.css',

        // 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css',
        '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css',
        // 'css/steps.css',
        // 'css/validation.css',
        'css/dblClick.mobile.css',
        // https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css',

        // 'css/bstimepicker.css',
        'plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css',
        'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css',
        'https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css'
    ];
    public $js = [

        // 'plugins/jquery/jquery.min.js',
        'plugins/bootstrap/js/bootstrap.bundle.min.js',
        'plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
        'dist/js/adminlte.min.js',
        // 'dist/js/demo.js',
        'plugins/jquery-mousewheel/jquery.mousewheel.js',
        'plugins/raphael/raphael.min.js',
        'plugins/jquery-mapael/jquery.mapael.min.js',
        // 'plugins/jquery-mapael/maps/usa_states.min.js',
        'plugins/chart.js/Chart.min.js',
        // 'dist/js/pages/dashboard2.js',

        // 'plugins/sparklines/sparkline.js',
        // 'plugins/jqvmap/jquery.vmap.min.js',
        // 'plugins/jqvmap/maps/jquery.vmap.usa.js',
        // 'plugins/jquery-knob/jquery.knob.min.js',
        'plugins/moment/moment.min.js',
        // 'plugins/daterangepicker/daterangepicker.js',

        'plugins/summernote/summernote-bs4.min.js',
        'plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
        'plugins/jquery-mousewheel/jquery.mousewheel.js',
        'plugins/raphael/raphael.min.js',
        'plugins/jquery-mapael/jquery.mapael.min.js',
        'plugins/jquery-mapael/maps/usa_states.min.js',
        'plugins/chart.js/Chart.min.js',
        'plugins/sweetalert2/sweetalert2.min.js',

        '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js',

        // 'https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js',
        // 'https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js ',
        // 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js',
        // 'js/app.js',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js',
        'https://cdn.jsdelivr.net/npm/sweetalert2@9', //Sweet Alert
        'Js/custom.js',
        'Js/modal.js',

        '//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js',
        '//cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js',
        '//cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js',
        '//cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js',
        '//cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js',
        '//cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js',



    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}

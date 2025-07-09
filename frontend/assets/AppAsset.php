<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'dist/css/adminlte.min.css',
        '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css',
        '//cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.13.1/font/bootstrap-icons.min.css',
        'plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css',
        '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css'
    ];
    public $js = [
        'dist/js/adminlte.min.js',
        'plugins/sweetalert2/sweetalert2.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/tinymce/7.9.1/tinymce.min.js',
        'js/tinymce.js',
        'js/custom.js',
        'js/accordion.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}

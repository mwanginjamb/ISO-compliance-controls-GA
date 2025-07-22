<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'odatav4' => [
            'class' => 'common\Library\Odatav4',
            'baseUrl' => "http://" . env('NAV_SERVER') . ":" . env('WS_ODATA_PORT') . "/" . env('NAV_INSTANCE') . "/ODataV4/Company('" . env('NAV_COMPANY') . "')/",
            'username' => env('NAV_USERNAME'),
            'password' => env('NAV_PASSWORD')
        ],
        'utility' => [
            'class' => 'common\Library\Utility'
        ],
        'sharepoint' => [
            'class' => 'common\Library\Sharepoint'
        ],
        'dashboard' => [
            'class' => 'common\Library\Dashboard'
        ],
        'assetManager' => [
            'appendTimestamp' => true
        ],
    ],
];

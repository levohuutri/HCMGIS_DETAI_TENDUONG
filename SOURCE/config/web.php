<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'timeZone' => 'Asia/Ho_Chi_Minh',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'layout' => 'main',
    'defaultRoute' => 'app/map-search',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => []
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => []
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => []
                ]
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'o60lq8tkQdSpe0YVVgspsSNPn-jIuk1q',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\modules\cms\models\AuthUser',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'htmlLayout' => 'layouts/main-html',
            'textLayout' => 'layouts/main-text',
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['loitn@hcmgis.vn' => 'HCMGIS PointCloud'],
            ],
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'host07.emailserver.vn',  // e.g. smtp.mandrillapp.com or smtp.gmail.com
                'username' => 'loitn@hcmgis.vn',
                'password' => 'loitnHCMGIS',
                'port' => '465', // Port 25 is a very common port too
                'encryption' => 'ssl', // It is often used, check your provider or mail server specs
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'app\controllers\CustomRule',
                ]
            ],
        ],
        'db' => $db,
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => '659731548199791',
                    'clientSecret' => '1a96cbc0d9cb729fa3f6d6c2556b2889',
                ],
                'google' => [
                    'class'        => 'yii\authclient\clients\Google',
                    'clientId'     => '537243962026-0epjtp6m404l45ae4ta1d52qu3rd3n2k.apps.googleusercontent.com',
                    'clientSecret' => 'WaZF2U72TrfMkF-oDt66Hf-3',
                ],
            ],
        ],
    ],
    'as beforeRequest' => [
        'class' => 'yii\filters\AccessControl',
        'rules' => [
            [
                'controllers' => ['site', 'api/point-cloud', 'app/map-search'],
                'allow' => true,
            ],
            [
                'controllers' => ['app/point-cloud'],
                'actions' => ['detail', 'index', 'map', 'get-comments', 'get-list', 'get-points-map', 'count-view', 'download', 'view', 'update-status', 'tag', 'get-points-by-tag'],
                'allow' => true,
            ],
            [
                'controllers' => ['app/user'],
                'actions' => ['point-cloud', 'get-user-points'],
                'allow' => true,
            ],
            [
                'controllers' => ['app/map'],
                'actions' => ['detail', 'index', 'get-public-maps'],
                'allow' => true,
            ],
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

$config['modules']['contrib'] = [
    'class' => 'app\modules\contrib\Module'
];

$config['modules']['cms'] = [
    'class' => 'app\modules\cms\Module'
];

$config['modules']['app'] = [
    'class' => 'app\modules\app\Module'
];

$config['modules']['api'] = [
    'class' => 'app\modules\api\Module'
];

return $config;

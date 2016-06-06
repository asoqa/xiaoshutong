<?php
$params = array_merge(require (__DIR__ . '/../../common/config/params.php'), require (__DIR__ . '/../../common/config/params-local.php'), require (__DIR__ . '/params.php'), require (__DIR__ . '/params-local.php'));

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => [
        'log'
    ],
    'modules' => [
        'v1' => [
            // 'basePath' => '@app/modules/v1'
            'class' => 'api\modules\v1\Module'
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'enableSession' => false,
            'loginUrl' => null
        ],
        
        // 'enableAutoLogin' => true,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => [
                        'error',
                        'warning'
                    ]
                ]
            ]
        ],
        
        // 'errorHandler' => [
        // 'errorAction' => 'api/error'
        // ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/user', 'v1/test']
                ]
                // '<module:\w+>/<controller:\w+>/<action:\wd+>' => '<module>/<controller>/<action>'
                
            ]
        ]
    ],
    'params' => $params
];

<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',
    'controllerNamespace' => 'api\controllers',
    'name' => 'api',
    'bootstrap' => ['log'],
    //'layout' => 'main',
    'modules' => [
        'v1' => [
            'basePath' => '@api/modules/v1',
            'class' => 'api\modules\v1\Module'
        ],
    ],
    'components' => [
        'request' => [
            'enableCsrfValidation' => false,
        ],
        /*'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                    if ($response->data !== null) {
                        $response->data = [
                            'success' => $response->isSuccessful,
                            'data' => $response->data,
                        ];
                        $response->statusCode = 200;
                    }
            },
        ],*/
        /*'response' => [
            'class' => 'yii\web\Response',
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            // ...
        ],*/
        'user' => [
            'identityClass' => 'api\modules\v1\models\User',
            'enableSession' => false,
            'enableAutoLogin' => true,
            'loginUrl' => null,
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
        /*'errorHandler' => [
            'errorAction' => 'post/error',
        ],*/
        /*'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules'=>[
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
            ],
        ],*/
        'urlManager' => [
            'enablePrettyUrl' => true,
            //'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => 'v1/<controller>/<action>',
                ['class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/post',
                    'pluralize' => false],
                ['class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/user',
                    'extraPatterns' => [
                        'GET,POST login' => 'login',
                    ],
                    'pluralize' => false],
            ],
        ],
        /*'authManager'=>[
            'class'=>'yii\rbac\DbManager',
            'defaultRoles' => ['guest', 'user'],
            //'cache' => 'yii\caching\FileCache',
        ],*/
    ],
    /*'as authenticator' => [
        'class' => 'api\common\QueryParamAuth',
        'allowActions' => [
            '/',
            '/v1/post',
            '/v1/post/index',
            '/v1/post/index',

        ]
    ],*/
    /*'as access' => [
        'class' => 'zc\rbac\components\AccessControl',
        'allowActions' => [
            '/',
            '/v1/post',
            '/v1/post/index',
            'home/captcha',
            'home/error',
            'user/logout',
            'user/login',
            //'some-controller/some-action',
            // The actions listed here will be allowed to everyone including guests.
            // So, 'admin/*' should not appear here in the production, of course.
            // But in the earlier stages of your development, you may probably want to
            // add a lot of actions here until you finally completed setting up rbac,
            // otherwise you may not even take a first step.
        ],
        'rules'        => [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
            [
                'actions' => ['logout'],
                'allow' => true,
                'roles' => ['@'],
            ],
            [
                'actions' => ['error'],
                'allow'   => true,
            ],
            [
                'actions' => ['login'],
                'allow'   => true,
                'roles'   => ['?'],
            ],
        ],
    ],*/
    'params' => $params,
];

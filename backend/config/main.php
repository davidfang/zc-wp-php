<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'defaultRoute'=>'home',
    'language'=>'zh-CN',
    'controllerNamespace' => 'backend\controllers',
    'name' => '管理系统',
    'bootstrap' => ['log'],
    'layout' => 'main',
    'modules' => [
        'rbac' => [
            'class' => 'zc\rbac\Module',
            //Some controller property maybe need to change.
            'controllerMap' => [
                'assignment' => [
                    'class' => 'zc\rbac\controllers\AssignmentController',
                    'userClassName' => 'app\models\AdminUser',
                ]
            ]
        ],
    ],
    'components' => [
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@vendor/zc/yii2-adminlte-asset/example-views/yiisoft/yii2-app'
                ],
            ],
        ],
        'request'=>[
            'enableCsrfValidation'=>false,
        ],
        'user' => [
            'identityClass' => 'app\models\AdminUser',
            'enableAutoLogin' => true,
            'loginUrl' => ['user/login'],
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
        'errorHandler' => [
            'errorAction' => 'home/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules'=>[
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
            ],
        ],
        'authManager'=>[
            'class'=>'yii\rbac\DbManager',
            'defaultRoles' => ['guest', 'user'],
            'cache' => 'yii\caching\FileCache',
        ],
        'assetManager'=>[
            'bundles'=>[
                'yii\web\JqueryAsset'=>[
                    'jsOptions'=>[
                        'position'=>\yii\web\View::POS_HEAD,
                    ]
                ]
            ]
        ],
    ],
    'as access' => [
        'class' => 'zc\rbac\components\AccessControl',
        'allowActions' => [
            '/',
            'home/*',
            'backend/reflushmenu',
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
    ],
    'params' => $params,
];

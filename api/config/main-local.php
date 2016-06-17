<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '5cri1v5pxN2TdTRRs6mP5Zv3TvFyS4QC',
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    //$config['modules']['gii'] = 'yii\gii\Module';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators'=>[
            'controller' => [
                'class' => 'zc\gii\controller\Generator',
                'templates' => [
                    'zc-gii' => '@vendor/zc/gii/controller/default',
                ]
            ],
            'crud' => [
                //'class' => 'yii\gii\generators\crud\Generator',
                'class' => 'zc\gii\crud\Generator',
                'templates' => [
                    'zc-gii' => '@vendor/zc/gii/crud/default',
                ]
            ],
            'module' => [
                'class' => 'zc\gii\module\Generator',
                'templates' => [
                    'zc-gii' => '@vendor/zc/gii/module/default',
                ]
            ],
            'form' => [
                'class' => 'zc\gii\form\Generator',
                'templates' => [
                    'zc-gii' => '@vendor/zc/gii/form/default',
                ]
            ],
            'model' => [
                'class' => 'zc\gii\model\Generator',
                'templates' => [
                    'zc-gii' => '@vendor/zc/gii/model/default',
                ]
            ],
            'extension' => [
                'class' => 'zc\gii\extension\Generator',
                'templates' => [
                    'zc-gii' => '@vendor/zc/gii/extension/default',
                ]
            ],

        ]

    ];
}

return $config;

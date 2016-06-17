<?php

use yii\grid\GridView;
use yii\helpers\Html;
use common\components\MyHelper;

$this->params['breadcrumbs'] = [
    '资源管理',
];
?>
    <p>
        <?= Html::a('添加资源/角色', 'create', ['class' => 'btn btn-sm btn btn-success']) ?>
    </p>

<?= GridView::widget([
    'dataProvider' => $dataprovider,
    'columns'      => [
        [
            'class'  => 'yii\grid\SerialColumn',
            'header' => '编号'
        ],
        'name:text:名称',
        'description:text:描述',
        'ruleName:text:规则名称',
        'createdAt:datetime:创建时间',
        [
            'class'    => 'yii\grid\ActionColumn',
            'header'   => '操作',
            'template' => '{view} {update} {delete}',
            'buttons'  => [
                'view'   => function ($url, $model, $key) {
                    return MyHelper::actionbutton(['/rbac/assignpermission', 'permission' => $key], 'view', ['title' => '分配权限资源']);
                },
                'update' => function ($url, $model, $key) {
                    return MyHelper::actionbutton('/rbac/update?id=' . $key, 'update');
                },
                'delete' => function ($url, $model, $key) {
                    return MyHelper::actionbutton('/rbac/delete?id=' . $key, 'delete');
                }
            ]
        ]
    ],
]) ?>
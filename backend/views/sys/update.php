<?php

/* @var $this yii\web\View */

$this->params['breadcrumbs'] = [
    [
        'label' => '菜单管理',
        'url'   => \yii\helpers\Url::toRoute(['sys/menu'])
    ],
    '编辑菜单'
];
?>

<?= $this->render('_form', ['model' => $model, 'plevel' => '']) ?>
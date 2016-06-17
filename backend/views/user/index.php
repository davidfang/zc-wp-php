<?php

header("Content-type:text/html;charset=utf-8");
use yii\bootstrap\Modal;
use kartik\widgets\ActiveForm;
use common\components\MyHelper;

$this->params['breadcrumbs'] = [
    '用户管理',
];
?>

<?php
Modal::begin([
    'id' => 'md',
    'header' => '<h4>添加用户</h4>',
    //'toggleButton' => ['label' => 'click me','class' => 'btn btn-sm btn-success',],
    'footer' => '<button type="button" class="btn btn-primary" onclick="sbmt()">确定</button>',
    'clientOptions'=>[
       // 'remote'=>Yii::$app->urlManager->createUrl('user/loadhtml'),//'http://admin/user/loadhtml'//
  //      'show'=>true,
  //      'keyboard'=>true,
  //      'backdrop'=>true
    ]
]);
echo 'Hello world';
Modal::end();
?>

<p>
    <?= \yii\helpers\Html::button('添加用户', [
        'class' => 'btn btn-sm btn-success',
//         'onclick' => '$("#md").modal();',
       'onclick'=>'loadhtml(null)'
    ]) ?>
</p>
<?= \yii\grid\GridView::widget([
    'dataProvider' => $dataprovider,
    'filterModel' => $searchmodel,
    'columns' => [
        'id',
        [
            'attribute'=>'username',
            //'filter'=>['admin'=>'系统管理员','demo'=>'屌丝管理员','hello'=>'嘻哈管理员'],
        ],
        'email',
        [
            'header' => '角色',

            'content' => function ($model) {
                $roles = Yii::$app->authManager->getRolesByUser($model->id);
                $roles = implode(',', array_keys($roles));
                return $roles;
            }
        ],
        [
            'header' => '操作',
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {update2} {delete}',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return $key == 1 ? null : MyHelper::actionbutton('/rbac/assignment/view?id=' . $key, 'view', ['title' => '查看/添加角色']);
                },
                'update' =>function($url, $model, $key){
                    return  MyHelper::actionbutton("javascript:loadhtml($key)", 'update', ['title' => '修改资料']);
                },
                'delete' => function ($url, $model, $key) {
                    return $key == 1 ? null : MyHelper::actionbutton($url, 'delete');
                }
            ]
        ],
    ],
]) ?>
<script>
    <?php $this->beginBlock('js_end') ?>
    function sbmt() {
        $('#userform').submit();
    }
    function loadhtml(id)
    {
        var parm = (id == null)?{}:{id:id};
        $('.modal-body').load('/user/loadhtml',{id:id},function(){
            $('#md').modal();
        })
    }
    <?php $this->endBlock(); ?>
</script>
<?php $this->registerJs($this->blocks['js_end'],\yii\web\View::POS_END) ?>
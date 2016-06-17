<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\components\MyHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AdmininfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Admininfos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admininfo-index">


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加 Admininfo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [   'format'=>'html',
                'label'=>'照片',
                'value'=>function($model){
                    return Html::img('/upload/user/'.$model->user->userphoto,['width'=>80,'height'=>80]);
                },
            ],
            ['format' => 'text',
                'label'=>'姓名',
                'value'=>'user.username',
                'attribute' => 'username',
                //'filter'=>Html::activeTextInput($searchModel, 'username',['class'=>'form-control']),
                /*function($model){
                return $model->user->username;
                },*/
            ],
            ['format' => 'text',
                'label'=>'邮箱',
                'value'=>'user.email',
                'attribute' => 'email',
                //'filter'=>Html::activeTextInput($searchModel, 'email',['class'=>'form-control']),
                /*function($model){
                return $model->user->username;
                },*/
            ],
            'age',
            'sex',
            'mobile',
            'city',
            'department',
            'status',
            'in_time',
             'id_number',
             'birthday',
             'birthday_month',
             // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn',
                'header'=>'操作',
                'template' => '{menu} {view} {update} {delete}',
                'buttons'  => [
                    'menu'   => function ($url, $model, $key) {
                        return $key == 1 ? null : MyHelper::actionbutton('/rbac/assignment/view?id=' . $key, 'icon-th-list', ['title' => '查看/添加角色']);
                    },

                ]

            ],
        ],
    ]); ?>

</div>

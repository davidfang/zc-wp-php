<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Admininfo */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Admininfos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admininfo-view">

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '你确定要删除此条信息?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [   'format'=>'html',
                'label'=>'照片',
                'value'=>Html::img('/upload/user/'.$model->user->userphoto,['width'=>150,'height'=>150]),
            ],
            [//'format' => 'text',
                'label'=>'姓名',
                'value'=>$model->user->username,
            ],
            [//'format' => 'text',
                'label'=>'邮箱',
                'value'=>$model->user->email,
            ],
            'city',
            'department',
            'status',
            'in_time',
            'id_number',
            'sex',
            'birthday',
            'birthday_month',
            'age',
            'mobile',
            //'created_at',
            //'updated_at',
        ],
    ]) ?>

</div>

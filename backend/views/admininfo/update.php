<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Admininfo */

$this->title = '编辑 Admininfo: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Admininfos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="admininfo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'model_primary' => $model_primary,
    ]) ?>

</div>

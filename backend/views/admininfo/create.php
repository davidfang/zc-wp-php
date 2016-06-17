<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Admininfo */

$this->title = '添加 Admininfo';
$this->params['breadcrumbs'][] = ['label' => 'Admininfos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admininfo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'model_primary' => $model_primary,
    ]) ?>

</div>

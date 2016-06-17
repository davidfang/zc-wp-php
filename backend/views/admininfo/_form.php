<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Admininfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="admininfo-form">

    <?php $form = ActiveForm::begin(); ?>
<div class="col-sm-6" >
    <?= $form->field($model_primary, 'id')->hiddenInput() ?>
    <?= $form->field($model_primary, 'username')->textInput() ?>
    <?= $form->field($model_primary,'password')->passwordInput() ?>
    <?= $form->field($model_primary,'password_repeat')->passwordInput() ?>
    <?= $form->field($model_primary, 'email')->textInput(['email' => true]) ?>

</div>
<div class="col-sm-6" >



    <?= $form->field($model, 'id')->hiddenInput() ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'department')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([10=>'在职',0=>'离职']) ?>

    <?= $form->field($model, 'in_time')->widget(\kartik\widgets\DatePicker::className(),['pluginOptions' => [
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true
    ]])  ?>

    <?= $form->field($model, 'id_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sex')->dropDownList(['男','女']) ?>

    <?= $form->field($model, 'birthday')->widget(\kartik\widgets\DatePicker::className(),['pluginOptions' => [
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true
    ]]) ?>

    <?= $form->field($model, 'birthday_month')->dropDownList(range(1,12)) ?>

    <?= $form->field($model, 'age')->textInput() ?>

    <?= $form->field($model, 'mobile')->textInput(['maxlength' => true]) ?>
</div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

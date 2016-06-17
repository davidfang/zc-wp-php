<?php

use kartik\widgets\ActiveForm;
$action = is_null($model->id)?'user/adduser':['user/update','id'=>$model->id];
?>
<?php $form = ActiveForm::begin([
    'id'=>'userform',
    'action'=>Yii::$app->urlManager->createUrl($action)
]) ?>
<?= $form->field($model,'username')->textInput(['readonly'=>!$model->isNewRecord]) ?>
<?= $form->field($model,'email')->textInput(['readonly'=>!$model->isNewRecord]) ?>
<?= $form->field($model,'password')->passwordInput() ?>
<?= $form->field($model,'password_repeat')->passwordInput() ?>
<?php $form->end() ?>
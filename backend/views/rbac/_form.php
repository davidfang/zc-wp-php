<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

?>

<div class="col-lg-6">
    <?php $form = ActiveForm::begin([
        'validationUrl' => Url::toRoute(['rbac/validateitemname']),
    ]) ?>
    <div class="form-group field-authitem-type required">
        <label class="control-label col-md-2" for="authitem-type">上级信息</label>
        <div class='col-md-10'>
            <?php if(!empty($father_info)){
                    echo \yii\widgets\DetailView::widget([
                        'model' => $father_info,
                        'attributes' => [
                            'name','description'
                        ],
                    ]);
                }else{
                echo '无';
            }
            ?>
        </div>
        <div class='col-md-offset-2 col-md-10'></div>
        <div class='col-md-offset-2 col-md-10'><div class="help-block"></div></div>
    </div>


    <?= $form->field($model,'type')->dropDownList([1=>'角色',2=>'资源']) ?>
    <?= $form->field($model, 'name', ['enableAjaxValidation' => true])->textInput()->hint('终极资源对照菜单中按照\'(module/)controller/action\'格式书写') ?>
    <?= $form->field($model, 'description')->textarea() ?>
    <?= Html::hiddenInput('id', $model->name) ?>

    <div class="form-group center">
        <?= Html::submitButton('提交', ['class' => 'btn btn-lg btn-primary']) ?>
    </div>

    <?php $form->end() ?>

</div>
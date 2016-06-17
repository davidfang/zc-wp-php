<?php

use yii\grid\GridView;
use yii\helpers\Html;
use common\components\MyHelper;
use backend\assets\RbacAsset;
RbacAsset::register($this);
$this->params['breadcrumbs'] = [
    '权限管理',
];
?>
    <p>
        <?= Html::a('添加资源/角色', 'create', ['class' => 'btn btn-sm btn btn-success']) ?>
    </p>
<div id="dd" class="dd" >
    <div class="col-sm-6">
        <div class="widget-box">
            <div class="widget-header header-color-blue2">
                <h4 class="lighter smaller">职称角色</h4>
            </div>

            <div class="widget-body">
                <div class="widget-main padding-8">
                    <div id="nestable">
                        <ol class="dd-list">
                            <?= $roles_tree['str'] ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="col-sm-6">
        <div class="widget-box">
            <div class="widget-header header-color-green2">
                <h4 class="lighter smaller">权限资源</h4>
            </div>

            <div class="widget-body">
                <div class="widget-main padding-8">
                    <div class=" dd-draghandle">
                        <ol class="dd-list">
                            <?= $permissions_tree['str'] ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script type="text/javascript">
        <?php $this->beginBlock('js_end') ?>
        $(function ($) {
            $('.dd').nestable()

            var target = null;

            $('.dd').on('mousedown', 'li', function (e) {
                target = e.target;
            });

            $('#dd').on('change', function () {
                var $child = $(target).parents("li"),
                    $father = $child.parents("li"),
                    child_id = $child.data('id'),
                    father_id = $father.data('id');
                $.ajax({
                    "type": "GET",
                    "url": "/rbac/add-child",
                    "data":{
                        father: father_id,
                        child:child_id
                    },
                    success: function (data) {
                        console.log(data);
                    }
                });
            });

            //$('[data-rel="tooltip"]').tooltip();

        });
        <?php $this->endBlock(); ?>
    </script>
<?php $this->registerJs($this->blocks['js_end'], \yii\web\View::POS_END) ?>
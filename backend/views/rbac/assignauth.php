<?php
/**
 * Created by PhpStorm.
 * User: olebar
 * Date: 2014/10/27
 * Time: 15:37:32
 */

use yii\widgets\DetailView;
$auth = Yii::$app->authManager;
$user = Yii::$app->user;

$this->params['breadcrumbs'] = [
    [
        'label'=>'角色管理',
        'url'=>'/rbac/roles',
    ],
    '角色分配角色'
];
?>

<?= DetailView::widget([
    'model'=>$model,
    'attributes'=>[
        'name',
        'description'
    ]
]) ?>
<div class="table-responsive">
    <table id="sample-table-1" class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th class="hidden-320">一级权限资源</th>
            <th>二三级权限资源</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($roles_tree as $f){
            $f_item = empty( $auth->getRole( $f['name'] ) ) ? $auth->getPermission($f['name']):$auth->getRole($f['name']);
            ?>
            <tr>
                <td style="width: 150px;">
                    <input type="checkbox" <?php if ($auth->hasChild($role,$f_item)): ?> checked <?php endif; ?>
                        <?php if ($role->name==$f['name']): ?> disabled <?php endif; ?>
                           onclick="ckbox(1,this)" name="<?= $f['_name'] ?>" id="<?= $f['name'] ?>" />
                    &nbsp;<?= $f['description'] ?></td>
                <td>
                    <?php if(!empty($f['children'])){ ?>
                        <?php foreach ($f['children'] as $son){
                            $son_item = is_null( $auth->getRole( $son['name'] ) ) ? $auth->getPermission($son['name']):$auth->getRole($son['name']);
                            ?>
                            <div class="col-xs-12 col-sm-12 widget-container-span ui-sortable">
                                <?php if(empty($son['children'])){ ?>
                                    <div class="widget-body">
                                        <div class="widget-body-inner" style="display: block;">
                                            <div class="widget-main">
                                                <input type="checkbox"
                                                       name="<?=  $f['_name'] . '_' .$son['_name'] ?>"
                                                       id="<?= $son['name'] ?>"
                                                    <?php if ($auth->hasChild($role,$son_item)): ?>
                                                        checked
                                                    <?php endif; ?>
                                                    <?php if ($role->name==$son['name']): ?> disabled <?php endif; ?>
                                                       onclick="ckbox(2,this)"/>
                                                <?= $son['description'] ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php }else{ ?>
                                    <div class="widget-box collapsed">
                                        <div class="widget-header widget-header-small">
                                            <h6>
                                                <input type="checkbox"
                                                       name="<?= $f['_name'] . '_' .$son['_name'] ?>"
                                                       id="<?= $son['name'] ?>"
                                                    <?php if ($auth->hasChild($role,$son_item)): ?>
                                                        checked
                                                    <?php endif; ?>
                                                    <?php if ($role->name==$son['name']): ?> disabled <?php endif; ?>
                                                       onclick="ckbox(2,this)"/>
                                                <?= $son['description'] ?>
                                            </h6>
                                            <div class="widget-toolbar">
                                                <a href="#" data-action="collapse">
                                                    <i class="icon-chevron-down"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <div class="widget-body-inner" style="display: block;">
                                                <div class="widget-main">
                                                    <?php foreach ($son['children'] as $gson):
                                                        $gson_item = empty( $auth->getRole( $gson['name'] ) ) ? $auth->getPermission($gson['name']):$auth->getRole($gson['name']);
                                                        ?>
                                                        <input type="checkbox"
                                                               name="<?= $f['_name'] . '_' .$son['_name'] .'_'.$gson['_name'] ?>"
                                                               id="<?= $gson['name'] ?>"
                                                            <?php if ($auth->hasChild($role,$gson_item)): ?>
                                                                checked
                                                            <?php endif; ?>
                                                            <?php if ($role->name==$gson['name']): ?> disabled <?php endif; ?>
                                                               onclick="ckbox(3,this)"/>&nbsp;
                                                        <?= $gson['description'] ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<script>
    function ckbox(level, o) {
        var name = $(o).attr('name');
        var id = $(o).attr('id');
        var val = $(o).val();
        var thischecked = $(o).is(':checked');
        //选中所有子孙
        $('input[name*=' + name + '_]').prop('checked', thischecked);
        //取消选中时判断父节点
        var arr = name.split('_');
        if (level == 3) {
            //如果3级菜单全都没选中，对应的2级菜单也取消选中
            var cntlv3 = $('input[name*=' + arr[0] + '_' + arr[1] + '_]:checked').size();
            if (cntlv3 > 0) {
                $('input[name=' + arr[0] + '_' + arr[1] + ']').prop('checked', true);
            } else {
                $('input[name=' + arr[0] + '_' + arr[1] + ']').prop('checked', false);
            }
        }
        if (level >= 2) {
            //如果2级菜单都没选中 1级菜单也取消选中
            var cntlv2 = $('input[name*=' + arr[0] + '_' + ']:checked').size();
            if (cntlv2 > 0) {
                $('#' + arr[0]).prop('checked', true);
            } else {
                $('#' + arr[0]).prop('checked', false);
            }
        }
        //更新数据
        var data = 'level=' + level + '&child=' + id + '&cntlv3=' + cntlv3 + '&cntlv2=' + cntlv2 + '&ck=' + thischecked + '&father=' + '<?= $rolename ?>';
        $.post('<?= \yii\helpers\Url::toRoute(['/rbac/assignauth']) ?>',data);
//        $.post('/rbac/assignauth',data);
    }
</script>
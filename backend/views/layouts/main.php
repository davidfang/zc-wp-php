<?php

use backend\assets\AppAsset;
use yii\helpers\Html;
use kartik\widgets\Alert;
use yii\helpers\Url;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="utf-8"/>
    <title><?= Yii::$app->params['webname'].'-'.(is_array($this->params['breadcrumbs'])?end($this->params['breadcrumbs']):'未定义') ?></title>

    <!--貌似没什么用-->
    <?= Html::cssFile('/css/font-awesome-ie7.min.css',['condition'=>'IE 7']) ?>
    <?= Html::cssFile('/css/ace-ie.min.css',['condition'=>'lte IE 8']) ?>
    <?= Html::jsFile('/js/html5shiv.js',['condition'=>'lte IE 9']) ?>
    <?= Html::jsFile('/js/respond.min.js',['condition'=>'lte IE 9']) ?>

    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="navbar navbar-default" id="navbar">
    <div class="navbar-container" id="navbar-container">
        <div class="navbar-header pull-left">
            <a href="#" class="navbar-brand" style="height: 40px">
                <small>
                    <i class="icon-star"></i>
                    <?= Yii::$app->params['webname'] ?>
                </small>
            </a><!-- /.brand -->
        </div>
        <!-- /.navbar-header -->

        <div class="navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">

                <li class="purple">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon-bell-alt icon-animated-bell"></i>
                        <span class="badge badge-important">8</span>
                    </a>

                    <ul class="pull-right dropdown-navbar navbar-pink dropdown-menu dropdown-caret dropdown-close">
                        <li class="dropdown-header">
                            <i class="icon-warning-sign"></i>
                            8 Notifications
                        </li>
                        <li>
                            <a href="#">
                                <div class="clearfix">
                                    <span class="pull-left">
                                        <i class="btn btn-xs no-hover btn-pink icon-comment"></i>
                                        New Comments
                                    </span>
                                    <span class="pull-right badge badge-info">+12</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                查看所有通知
                                <i class="icon-arrow-right"></i>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="light-blue">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <img class="nav-user-photo" src="/upload/user/<?= is_null(Yii::$app->user->identity->userphoto)?'default.jpg':Yii::$app->user->identity->userphoto ?>" alt="Jason's Photo"/>
                            <span class="user-info">
                                <small>Welcome,</small>
                                <?= Yii::$app->user->identity->username ?>
                            </span>
                        <i class="icon-caret-down"></i>
                    </a>

                    <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <li>
                            <a href="<?= Yii::$app->urlManager->createUrl(['user/setphoto']) ?>">
                                <i class="icon-user"></i>
                                设置头像
                            </a>
                        </li>
                        <li>
                            <a href="<?= Yii::$app->urlManager->createUrl(['user/changepwd']) ?>">
                                <i class="icon-edit"></i>
                                修改密码
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?= Yii::$app->urlManager->createUrl(['user/logout']) ?>">
                                <i class="icon-off"></i>
                                退出
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- /.ace-nav -->
        </div>
        <!-- /.navbar-header -->
    </div>
    <!-- /.container -->
</div>

<div class="main-container" id="main-container">

<div class="main-container-inner">
<a class="menu-toggler" id="menu-toggler" href="#">
    <span class="menu-text"></span>
</a>
<div class="sidebar" id="sidebar">
    <div class="sidebar-shortcuts" id="sidebar-shortcuts">
        <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
            <button class="btn btn-success">
                <i class="icon-signal"></i>
            </button>

            <button class="btn btn-info">
                <i class="icon-pencil"></i>
            </button>

            <button class="btn btn-warning">
                <i class="icon-group"></i>
            </button>

            <button class="btn btn-danger">
                <i class="icon-cogs"></i>
            </button>
        </div>

        <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
            <span class="btn btn-success"></span>

            <span class="btn btn-info"></span>

            <span class="btn btn-warning"></span>

            <span class="btn btn-danger"></span>
        </div>
    </div>
    <!--菜单伸缩-->
    <div class="sidebar-collapse" id="sidebar-collapse">
        <i class="icon-double-angle-left" data-icon1="icon-double-angle-left"
           data-icon2="icon-double-angle-right"></i>
    </div>
    <!-- #sidebar-shortcuts -->
    <?= Yii::$app->cache->get('menulist-'.Yii::$app->user->id) ?>
    <!-- /.nav-list -->
</div>
<div class="main-content">
    <div class="breadcrumbs" id="breadcrumbs">
        <?= \yii\widgets\Breadcrumbs::widget([
            'itemTemplate' => "<li>{link}</li>\n",
            'links'=>$this->params['breadcrumbs']?:['未定义'],
        ]) ?>
        <!-- .breadcrumb -->
        <div class="nav-search" id="nav-search">
            <form class="form-search">
                <span class="input-icon">
                    <input type="text" placeholder="Search ..." class="nav-search-input"
                           id="nav-search-input" autocomplete="off"/>
                    <i class="icon-search nav-search-icon"></i>
                </span>
            </form>
        </div>
        <!-- #nav-search -->
    </div>
    <div class="page-content">
        <!-- /.page-header -->
        <div class="row">
            <?php if ($msg = Yii::$app->session->getFlash('success')): ?>
                <?=
                Alert::widget([
                    'body' => ($msg==1)?'操作成功':$msg,
                    'delay' => 1000,
                    'type' => Alert::TYPE_SUCCESS,
                ]) ?>
            <?php endif; ?>
            <?php if (Yii::$app->session->getFlash('fail')): ?>
                <?=
                Alert::widget([
                    'body' => Yii::$app->session->getFlash('fail'),
                    'type' => Alert::TYPE_DANGER,
                ]) ?>
            <?php endif; ?>
            <?= $content ?>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.page-content -->
</div>
<!-- /.main-content -->
    <div class="ace-settings-container" id="ace-settings-container">
        <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
            <i class="icon-cog bigger-150"></i>
        </div>

        <div class="ace-settings-box" id="ace-settings-box">
            <div>
                <div class="pull-left">
                    <select id="skin-colorpicker" class="hide">
                        <option data-skin="default" value="#438EB9">#438EB9</option>
                        <option data-skin="skin-1" value="#222A2D">#222A2D</option>
                        <option data-skin="skin-2" value="#C6487E">#C6487E</option>
                        <option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
                    </select>
                </div>
                <span>&nbsp; 选择皮肤</span>
            </div>

            <div>
                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-navbar" />
                <label class="lbl" for="ace-settings-navbar"> 固定导航条</label>
            </div>

            <div>
                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-sidebar" />
                <label class="lbl" for="ace-settings-sidebar"> 固定滑动条</label>
            </div>

            <div>
                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-breadcrumbs" />
                <label class="lbl" for="ace-settings-breadcrumbs">固定面包屑</label>
            </div>

            <div>
                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" />
                <label class="lbl" for="ace-settings-rtl">切换到左边</label>
            </div>

            <div>
                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-add-container" />
                <label class="lbl" for="ace-settings-add-container">
                    切换窄屏
                    <b></b>
                </label>
            </div>
        </div>
    </div>
<!-- /#ace-settings-container -->
</div>
<!-- /.main-container-inner -->

<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
    <i class="icon-double-angle-up icon-only bigger-110"></i>
</a>
</div>


<?php $this->endBody() ?>
<script type="text/javascript">
    jQuery(function ($) {
        //菜单收缩
        var route = '/<?= Yii::$app->requestedRoute ?>';
        if($("#sidebar li:has(a[href='"+route+"'])").length==0)
            route = '<?= Yii::$app->session->get('referrerroute') ?>';
        $("#sidebar li:has(a[href='"+route+"'])").attr('class','active open');
        //侧边收缩
        var sidebar = $('#sidebar');
        if($.cookie('sidebar')!='')
        {
            sidebar.attr('class', $.cookie('sidebar'));
        }
        sidebar.click(function(){
            $.cookie('sidebar', sidebar.attr('class'), {path: '/'});
        })
    })
</script>
</body>
</html>
<?php $this->endPage() ?>
# ace yii
## Install up:
1.  git clone https://github.com/davidfang/ace.git
2.  运行`composer update --prefer-dist` 安装yii2核心库文件
3.  创建数据库 `ace` 编码 `utf8-unicode-ci`
4.  在项目根目录下运行`init`初始化项目(生成入口脚本、创建runtime目录等)
5.  删除`common/config/main-local.php`里的db配置(采用`common/config/db.php`里的就可以)，修改数据库配置信息
5.  运行`yii migrate`导入菜单表`ace_menu`和用户表`ace_admin_user` 权限表等

<?php

use yii\db\Schema;
use yii\db\Migration;

class m141022_124022_create_menutable extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%menu}}', [
            'id' => Schema::TYPE_PK,
            'menuname' => Schema::TYPE_STRING . '(32) NOT NULL',
            'parentid'=> Schema::TYPE_SMALLINT .' NOT NULL DEFAULT 0',
            'route' => Schema::TYPE_STRING . '(32) NOT NULL',
            'menuicon' => Schema::TYPE_STRING . '(16) NOT  NULL DEFAULT "icon-book"',

            'level' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
        ], $tableOptions);
        $sql = "INSERT INTO {{%menu}} (`id`, `menuname`, `parentid`, `route`, `menuicon`, `level`) VALUES
(1, '系统设置', 0, 'sys', 'icon-book', 1),
(2, '用户管理', 0, 'user', 'icon-book', 1),
(3, '菜单管理', 1, 'menu', 'icon-book', 2),
(4, '权限管理', 1, 'rbac', 'icon-book', 2),
(5, '设置菜单', 3, 'sys/menu', 'icon-book', 3),
(6, '刷新菜单', 3, 'backend/reflushmenu', 'icon-book', 3),
(7, '角色管理', 4, 'rbac/role', 'icon-book', 3),
(8, '路由管理', 4, 'rbac/route', 'icon-book', 3),
(9, '分配权限', 4, 'rbac/permission', 'icon-book', 3),
(10, '规则管理', 4, 'rbac/rule', 'icon-book', 3),
(11, '分配角色', 4, 'rbac/assignment', 'icon-book', 3),
(12, '用户列表', 2, 'admininfo/index', 'icon-book', 3),
(13, '用户权限', 2, 'user/index', 'icon-book', 3);";
        $this->execute($sql);
    }

    public function down()
    {
        echo "m141022_124022_create_menutable cannot be reverted.\n";

        return false;
    }
}

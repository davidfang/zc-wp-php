<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

use yii\base\InvalidConfigException;
use yii\db\Schema;
use yii\rbac\DbManager;

/**
 * Initializes RBAC tables
 *
 * @author Alexander Kochetov <creocoder@gmail.com>
 * @since 2.0
 */
class m140506_102106_rbac_init extends \yii\db\Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    public function up()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable($authManager->ruleTable, [
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
        ], $tableOptions);

        $this->createTable($authManager->itemTable, [
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'description' => Schema::TYPE_TEXT,
            'rule_name' => Schema::TYPE_STRING . '(64)',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
            'FOREIGN KEY (rule_name) REFERENCES ' . $authManager->ruleTable . ' (name) ON DELETE SET NULL ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createIndex('idx-auth_item-type', $authManager->itemTable, 'type');

        $this->createTable($authManager->itemChildTable, [
            'parent' => Schema::TYPE_STRING . '(64) NOT NULL',
            'child' => Schema::TYPE_STRING . '(64) NOT NULL',
            'PRIMARY KEY (parent, child)',
            'FOREIGN KEY (parent) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY (child) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

        $this->createTable($authManager->assignmentTable, [
            'item_name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'user_id' => Schema::TYPE_STRING . '(64) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (item_name, user_id)',
            'FOREIGN KEY (item_name) REFERENCES ' . $authManager->itemTable . ' (name) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $sql = "INSERT INTO {$authManager->itemTable} (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('Gii', 2, 'gii/*', NULL, NULL, 1448357391, 1448357391),
('/*', 2, NULL, NULL, NULL, 1450750195, 1450750195),
('DEBUG', 2, 'debug/*', NULL, NULL, 1448357391, 1448357391),
('分配权限', 2, 'rbac/permission/*', NULL, NULL, 1448008362, 1448008362),
('分配角色', 2, 'rbac/assignment/*', NULL, NULL, 1448008423, 1448008423),
('刷新菜单', 2, 'backend/reflushmenu', NULL, NULL, 1448008211, 1448008211),
('权限管理', 2, 'rbac', NULL, NULL, 1448008144, 1448008144),
('权限管理员', 1, '权限管理员', NULL, NULL, 1448008801, 1448008801),
('用户列表', 2, 'admininfo/*', NULL, NULL, 1448008542, 1448008542),
('用户权限', 2, 'user/*', NULL, NULL, 1448008615, 1448357580),
('用户管理', 2, 'user', NULL, NULL, 1448008059, 1448008059),
('用户管理员', 1, '用户管理员', NULL, NULL, 1448009037, 1448009037),
('系统设置', 2, 'sys/*', NULL, NULL, 1448008041, 1448536832),
('菜单管理', 2, 'menu', NULL, NULL, 1448008121, 1448008121),
('菜单管理员', 1, '菜单管理员', NULL, NULL, 1448008820, 1448008820),
('规则管理', 2, 'rbac/rule/*', NULL, NULL, 1448008383, 1448008383),
('角色管理', 2, 'rbac/role/*', NULL, NULL, 1448008306, 1448008306),
('设置菜单', 2, 'sys/menu', NULL, NULL, 1448008183, 1448008183),
('超级管理员', 1, '超级管理员', NULL, NULL, 1448008740, 1448008740),
('路由管理', 2, 'rbac/route/*', NULL, NULL, 1448008340, 1448008340);";
        $this->execute($sql);
        $sql = "INSERT INTO {$authManager->itemChildTable} (`parent`, `child`) VALUES
('超级管理员', '/*'),
('超级管理员', 'DEBUG'),
('权限管理', '分配权限'),
('权限管理', '分配角色'),
('菜单管理', '刷新菜单'),
('权限管理员', '权限管理'),
('超级管理员', '权限管理员'),
('用户管理', '用户列表'),
('用户管理员', '用户列表'),
('用户管理', '用户权限'),
('用户管理员', '用户权限'),
('用户管理员', '用户管理'),
('超级管理员', '用户管理员'),
('权限管理员', '系统设置'),
('菜单管理员', '系统设置'),
('菜单管理员', '菜单管理'),
('超级管理员', '菜单管理员'),
('权限管理', '规则管理'),
('权限管理', '角色管理'),
('菜单管理', '设置菜单'),
('权限管理', '路由管理');";
        $this->execute($sql);
        $sql = "INSERT INTO {$authManager->assignmentTable} (`item_name`, `user_id`, `created_at`) VALUES
('权限管理员', '3', 1448014511),
('用户管理员', '2', 1448009650),
('菜单管理员', '4', 1448014481),
('超级管理员', '1', 1448359155),
('超级管理员', '5', 1448012115);
";
        $this->execute($sql);

    }

    public function down()
    {
        $authManager = $this->getAuthManager();
        $this->db = $authManager->db;

        $this->dropTable($authManager->assignmentTable);
        $this->dropTable($authManager->itemChildTable);
        $this->dropTable($authManager->itemTable);
        $this->dropTable($authManager->ruleTable);
    }
}

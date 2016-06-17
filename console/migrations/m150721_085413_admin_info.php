<?php

use yii\db\Schema;
use yii\db\Migration;

class m150721_085413_admin_info extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }



        $time = time();
        $sql = "CREATE TABLE  {{%admin_info}} (
  `id` int(11) NOT NULL COMMENT '用户ID',
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '部门',
  `status` set('在职','劳务','离职') COLLATE utf8_unicode_ci DEFAULT '在职' COMMENT '状态',
  `in_time` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '入职时间',
  `id_number` varchar(18) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '身份证号',
  `sex` set('男','女') COLLATE utf8_unicode_ci DEFAULT '男' COMMENT '性别',
  `birthday` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '生日',
  `birthday_month` int(2) DEFAULT NULL COMMENT '生日月份',
  `age` int(2) DEFAULT NULL COMMENT '年龄',
  `mobile` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '联系电话',
  `created_at` int(13) DEFAULT NULL COMMENT '建立时间',
  `updated_at` int(13) DEFAULT NULL COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='管理人员信息表';";
        $this->execute($sql);
        $sql = "INSERT INTO {{%admin_info}} (`id`, `city`, `department`, `status`, `in_time`, `id_number`, `sex`, `birthday`, `birthday_month`, `age`, `mobile`, `created_at`, `updated_at`) VALUES
(1, '上海', '人事部', '在职', '2015/01/01', '41020519821019', '男', '19850107', 1, 30, '13612345678', 2147483647, 2147483647),
(2, '北京', '市场部', '在职', '20150203', '41020519821019', '男', '19850805', 8, 30, '13512345678', 2147483647, 2147483647),
(3, '上海', '技术部', '在职', '2015/01/01', '41020519821019', '男', '19850107', 1, 30, '13612345678', 2147483647, 2147483647),
(4, '北京', '财务部', '在职', '20150203', '41020519821019', '男', '19850805', 8, 30, '13512345678', 2147483647, 2147483647);
";
        $this->execute($sql);
        $sql = "ALTER TABLE {{%admin_info}} ADD PRIMARY KEY (`id`);";
        $this->execute($sql);
    }

    public function down()
    {
        echo "m150721_085413_admin_info cannot be reverted.\n";
        $this->dropTable('{{%admin_info}}');
        return false;
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}

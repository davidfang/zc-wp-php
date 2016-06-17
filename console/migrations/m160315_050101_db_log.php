<?php

use yii\db\Schema;

class m160315_050101_db_log extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%db_log}}', [
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT  COMMENT "序号"',
            'table' => 'varchar(45) NOT NULL DEFAULT ""  COMMENT "数据表名"',
            'unique_key' => 'varchar(45) NOT NULL DEFAULT ""  COMMENT "数据表唯一键"',
            'uri' => 'varchar(200) NOT NULL DEFAULT ""  COMMENT "请求url"',
            'action' => 'varchar(200) NOT NULL DEFAULT ""  COMMENT "动作名称"',
            'type' => 'enum("INSERT","UPDATE","DELETE") NOT NULL DEFAULT "UPDATE"  COMMENT "动作类型"',
            'prev_data' => 'text NOT NULL DEFAULT ""  COMMENT "变更前数据 json"',
            'cur_data' => 'text NOT NULL DEFAULT ""  COMMENT "变更后数据 json"',
            'process_time' => 'int(10) unsigned NOT NULL DEFAULT "0"  COMMENT "时间"',
            'process_user' => 'varchar(100) NOT NULL DEFAULT ""  COMMENT "人"',
            'process_ip' => 'varchar(45) NOT NULL DEFAULT ""  COMMENT "ip"',
            'process_user_agent' => 'varchar(200) NOT NULL DEFAULT ""  COMMENT "http user agent"',
            'PRIMARY KEY ([[id]])',
        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%db_log}}');
    }
}

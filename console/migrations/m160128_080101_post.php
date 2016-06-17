<?php

use yii\db\Schema;

class m160128_080101_post extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%post}}', [
            'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT  ',
            'name' => 'varchar(30)  DEFAULT NULL  COMMENT "广告位名称"',
            'sort' => 'int(10) unsigned NOT NULL DEFAULT "0"  COMMENT "显示排序"',
            'image' => 'varchar(255)  DEFAULT NULL  COMMENT "广告图片"',
            'jump_url' => 'varchar(30)  DEFAULT NULL  COMMENT "跳转地址"',
            'jump_type_r' => 'enum("0","1") NOT NULL DEFAULT "1"  COMMENT "跳转类型 0 自定义 1 单品页"',
            'status' => 'enum("1","2","3","4") NOT NULL DEFAULT "2"  COMMENT "状态，1 停止 2 暂停 3 删除 4启用"',
            'start_date' => 'date NOT NULL DEFAULT "0000-00-00"  COMMENT "开始时间"',
            'end_date' => 'date NOT NULL DEFAULT "0000-00-00"  COMMENT "结束时间"',
            'date_type_r' => 'enum("0","1") NOT NULL DEFAULT "0"  COMMENT "时间类型 0 使用已定义时间 1 不限时间"',
            'PRIMARY KEY ([[id]])',
        ], $tableOptions);
    $sql = "INSERT INTO {{%post}} (`id`,`name`,`sort`,`image`,`jump_url`,`jump_type_r`,`status`,`start_date`,`end_date`,`date_type_r`) VALUES

            ('29','qqqq','11','162690','www.123.com','0','3','2015-12-09','2016-12-10','0'),
            ('28','不求上进上','5','162970','http://www.163.com','1','3','2015-12-08','2015-12-31','1'),
            ('5','sssssss','2','uploads/20151110105003-853.png','http://www.baidu.com','1','3','2015-11-01','2016-11-20','1'),
            ('9','gdfddfgdfg','2','172908','http://www.baidu.com','1','3','2015-10-26','2016-12-03','1'),
            ('11','机器猫001','1','14728','https://www.123.com','1','4','2015-11-23','2015-11-24','1'),
            ('14','机器猫','3','14752','https://ww.234.com','1','4','2015-11-24','2015-11-25','0'),
            ('15','大师','2','14753','https://www.321.com','1','3','2015-11-24','2015-11-25','1'),
            ('16','大大','3','14754','sdadasdada','0','3','2015-11-24','2015-11-25','0'),
            ('19','机器猫','2','15766','https://www.123.com','0','3','2015-11-27','2015-11-28','1'),
            ('18','快乐','7','14950','https://www.123.com','0','3','2015-11-25','2015-11-26','0'),
            ('20','sd ','2','15767','http;//www.123.com','1','3','2015-11-12','2015-11-13','0'),
            ('21','we','2','15768','https://www.123.com','0','3','2015-11-23','2015-11-24','0'),
            ('22','gdfddfgdfgss','5','16704','http://www.163.com','1','3','2015-12-17','2015-12-31','1'),
            ('23','问问','2','16706','https://www.123.com','1','3','2015-12-25','2015-12-27','1'),
            ('24','123','123','156210','www.baidu.com','0','3','2015-12-08','2015-12-31','1'),
            ('25','机器猫','12','156211','www.sohu.com','0','3','2015-12-08','2015-12-09','0'),
            ('26','电视','2','156665','www.123.com','1','3','2015-12-09','2015-12-10','1'),
            ('27','是','2','156666','www.123.com','1','3','2015-12-09','2015-12-10','1'),
            ('30','2','2','162743','www.sohu.com','1','3','2015-12-09','2015-12-10','1'),
            ('31','hu  ji','6','172518','www.123.com','1','3','2015-12-11','2015-12-12','0'),
            ('32','231','2','172909','www.123.com','1','3','2016-01-27','2016-01-28','1'),
            ('33','请问','1','172910','额q','1','3','2015-12-18','2015-12-31','0');";
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('{{%post}}');
    }
}

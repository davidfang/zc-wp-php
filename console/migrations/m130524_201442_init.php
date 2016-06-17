<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        /*$this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING . ' NOT NULL',

            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);*/
        $this->createTable('{{%user}}', [
            'id' => Schema::TYPE_PK,
            'fromusername' => Schema::TYPE_STRING . '  NULL',
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'access_token' => Schema::TYPE_STRING . '  NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING . ' NOT NULL',

            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'userphoto' => Schema::TYPE_STRING . '(64)  NULL DEFAULT "nophoto.jpg"',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
        $Security = new \yii\base\Security();
        $pw1 = $Security->generatePasswordHash('admin');
        $auth_key1 = $Security->generateRandomString();
        $access_token1 = $Security->generateRandomString();
        $pw2 = $Security->generatePasswordHash('user');
        $auth_key2 = $Security->generateRandomString();
        $access_token2 = $Security->generateRandomString();
        $time = time();
        $sql = "INSERT INTO {{%user}} (`id`, `username`, `access_token`, `password_hash`,`auth_key`,`email`,`created_at`,`updated_at`) VALUES
(1, 'test1', '$access_token1', '$pw1','$auth_key1','test1@abc.com',$time,$time),
(2, 'test2', '$access_token2', '$pw2','$auth_key2','test2@abc.com',$time,$time);";
        $this->execute($sql);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}

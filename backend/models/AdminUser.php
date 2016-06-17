<?php
/**
 * Created by David
 * User: David.Fang
 * Date: 2015/7/20
 * Time: 15:17
 */

namespace app\models;


use common\models\User;
use yii\base\Security;
use yii\behaviors\TimestampBehavior;

class AdminUser extends User {
    public $password;
    public $oldpassword;
    public $password_repeat;
    public $verifyCode;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_user}}';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username','unique'],
            [['username', 'password'], 'required'],
            [['email'],'required','on'=>['create']],
            ['email','email'],
            [['password_repeat'],'required','on'=>['create','update','chgpwd']],
            [['oldpassword','password_repeat'],'required','on'=>['chgpwd','update']],
            //['verifyCode','captcha','on'=>['create','chgpwd']],//
            ['oldpassword','validateOldPassword'],
            [['username', 'password', 'userphoto'], 'string', 'max' => 255],
            ['password_repeat','compare','compareAttribute'=>'password']
        ];
    }
    public function validateOldPassword()
    {
        $user = self::findOne($this->id);

        if (!$user || !$user->validatePassword($this->oldpassword)) {
            $this->addError('oldpassword', '原始密码错误');
        }
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'oldpassword' => '原密码',
            'password' => '密码',
            'password_repeat'=>'重复密码',
            'verifyCode'=>'验证码',
            'email'=>'邮箱',
            'userphoto'=>'用户头像',
        ];
    }
    /*public function beforeSave($insert)
    {
        if($this->isNewRecord || $this->password_hash!=$this->oldAttributes['password'])
            $this->password_hash = \Yii::$app->security->generatePasswordHash($this->password_hash);
        return true;
    }*/
    public function getInfo(){
        return $this->hasOne(AdminInfo::className(), ['id' => 'id']);
    }
    /**
     * 关联获取角色
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(AuthAssignment::className(),['user_id'=>'id']);
    }
    public static function findByusername($username)
    {
        return static::find()->where('username=:u',[':u'=>$username])->one();
    }

    /**
     * 添加用户
     * @return $this|null
     */
    public function addUser(){
        if($this->validate()) {
            $this->setPassword($this->password);
            $this->generateAuthKey();
            if ($this->save(false)) {
                return $this;
            }
        }
        return null;
    }
}
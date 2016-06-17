<?php
/**
 * Created by David
 * User: David.Fang
 * Date: 2015/7/20
 * Time: 15:17
 */

namespace api\modules\v1\models;


use common\models\User as commonUser;
use yii\behaviors\TimestampBehavior;

class User extends commonUser {
    public $password;
    public $oldpassword;
    public $password_repeat;
    public $verifyCode;
    //public $access_token;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
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
            [['username', 'password'], 'required','on'=>['login']],
            [['username', 'password','email'], 'required','on'=>['create']],
            ['email','email'],
            [['password_repeat'],'required','on'=>['create','update','chgpwd']],
            [['oldpassword','password_repeat'],'required','on'=>['chgpwd','update']],
            //['verifyCode','captcha','on'=>['create','chgpwd']],//
            ['oldpassword','validateOldPassword','on' =>'chgpwd'],
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
            'access_token' ,
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
    /*public function getInfo(){
        return $this->hasOne(AdminInfo::className(), ['id' => 'id']);
    }*/
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

    /**
     * 通过 access_token查找用户
     * @param mixed $token
     * @param null $type
     * @return null|static
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if (empty($token)) {
            return null;
        }
        $expire = \Yii::$app->params['user.AccessTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        if($timestamp + $expire >= time()) {
            return static::findOne(['access_token' => $token]);
        }else{
            return null;
        }
    }
    /**
     * Generates new access_token
     */
    public function generateAccessToken()
    {
        $this->access_token = \Yii::$app->security->generateRandomString() . '_' . time();
        $this->save();
    }

    /**
     * Logs in a user.
     *
     * After logging in a user, you may obtain the user's identity information from the [[identity]] property.
     * If [[enableSession]] is true, you may even get the identity information in the next requests without
     * calling this method again.
     *
     * The login status is maintained according to the `$duration` parameter:
     *
     * - `$duration == 0`: the identity information will be stored in session and will be available
     *   via [[identity]] as long as the session remains active.
     * - `$duration > 0`: the identity information will be stored in session. If [[enableAutoLogin]] is true,
     *   it will also be stored in a cookie which will expire in `$duration` seconds. As long as
     *   the cookie remains valid or the session is active, you may obtain the user identity information
     *   via [[identity]].
     *
     * Note that if [[enableSession]] is false, the `$duration` parameter will be ignored as it is meaningless
     * in this case.
     *
     * @param IdentityInterface $identity the user identity (which should already be authenticated)
     * @param integer $duration number of seconds that the user can remain in logged-in status.
     * Defaults to 0, meaning login till the user closes the browser or the session is manually destroyed.
     * If greater than 0 and [[enableAutoLogin]] is true, cookie-based login will be supported.
     * Note that if [[enableSession]] is false, this parameter will be ignored.
     * @return boolean whether the user is logged in
     */
    public function login(IdentityInterface $identity, $duration = 0)
    {
        if ($this->beforeLogin($identity, false, $duration)) {
            $this->switchIdentity($identity, $duration);
            $id = $identity->getId();
            $ip = Yii::$app->getRequest()->getUserIP();
            if ($this->enableSession) {
                $log = "User '$id' logged in from $ip with duration $duration.";
            } else {
                $log = "User '$id' logged in from $ip. Session not enabled.";
            }
            Yii::info($log, __METHOD__);
            $this->afterLogin($identity, false, $duration);
        }

        return !$this->getIsGuest();
    }
}
<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\MobileVerification;
use Yii;
use api\modules\v1\models\User;
use yii\rest\ActiveController;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;

class UserController extends ActiveController
{
    public function behaviors()
    {
        return parent::behaviors();
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    // restrict access to
                    'Origin' => ['http://node.dev', 'https://node.dev'],
                    'Access-Control-Request-Method' => ['POST', 'GET', 'PUT'],
                    // Allow only POST and PUT methods
                    'Access-Control-Request-Headers' => ['X-Wsse'],
                    // Allow only headers 'X-Wsse'
                    //'Access-Control-Allow-Credentials' => true,
                    // Allow OPTIONS caching
                    //'Access-Control-Max-Age' => 3600,
                    // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                    //'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
                ],

            ],
        ];
    }

    /*public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        $result = ['data'=>$result,'status'=>true];
        return $this->serializeData($result);
    }*/
    public $modelClass = 'api\common\models\User';

    /**
     * 登录
     * method:POST OR GET
     *  参数：
     *  POST:
     *     username
     *     password
     *  GET:
     *     access_token
     * 返回：
     * "result": "success", //failed
     * "status":true ,//false
     * "msg":"登录成功",//
     * "data":{
     * "access_token": "xxxxxxxxxxxxxxxxxxxx"
     * }
     *
     * "error":{
     *
     * }
     *
     * @return array
     */
    public function actionLogin()
    {
        $result = false;
        $accessToken = Yii::$app->request->get('access_token');
        if ($accessToken) {
            if (User::findIdentityByAccessToken($accessToken)) {
                $result = true;
            }
        } elseif (Yii::$app->request->post('username') && Yii::$app->request->post('password')) {
            $user = User::findByUsername(Yii::$app->request->post('username'));
            if ($user && $user->validatePassword(Yii::$app->request->post('password'))) {
                $user->generateAccessToken();
                $accessToken = $user->access_token;
                $result = true;
            }
        }

        if ($result) {
            return ['status' => true,
                'msg' => '登录成功',
                'data' => [
                    'access-token' => $accessToken,
                ]
            ];
        } else {
            return ['status' => false, 'msg' => '登录失败'];
        }
    }

    /**
     * 登录
     * method:POST
     *  参数：
     *  POST:
     *     mobile
     *     password
     * 返回：
     * "result": "success", //failed
     * "access_token": "xxxxxxxxxxxxxxxxxxxx"
     *
     * @return array
     */
    public function actionSignIn()
    {
        $result = ['status' => false, 'msg' => '登录失败',];
        $userModel = new User(['scenario' => 'signIn']);
        if ($userModel->load(Yii::$app->request->post(), '') && $userModel->validate()) {
            $user = User::findByMobile(Yii::$app->request->post('mobile'));
            if (!$user) {
                $result['error'] = ['mobile' => '账号不存在'];
            } else {
                if ($user->validatePassword(Yii::$app->request->post('password'))) {
                    $user->generateAccessToken();
                    $accessToken = $user->access_token;
                    $result = ['status' => true, 'msg' => '登录成功', 'data' => ['access_token' => $accessToken]];
                } else {
                    $result['error'] = ['password' => '密码错误'];
                }
            }
        } else {//手机号 密码输入错误
            $result['error'] = $userModel->getFirstErrors();
        }
        return $result;
    }

    /**
     * 获取手机验证码
     * @param $mobile
     * @return array
     */
    public function actionGetMobileVerification($mobile)
    {
        $userModel = new \api\modules\v1\models\User();
        $userModel->scenario = 'register';
        $userModel->mobile = $mobile;
        if ($userModel->validate()) {
            $code = MobileVerification::generator($mobile);
            return ['status' => true, 'msg' => '验证码发送成功,请查收手机短信。', 'code' => $code];
        } else {
            return ['status' => false, 'msg' => $userModel->getFirstError('mobile')];
        }
    }

    /**
     * 用户注册
     * @return array
     */
    public function actionSignUp()
    {
        $userModel = new User(['scenario' => 'create']);
        if ($userModel->load(Yii::$app->request->post(), '')) {
            if ($userModel->validate() && $userModel->addUser()) {
                return ['status' => true, 'msg' => '注册成功。', 'data' => ['access_token' => $userModel->access_token]];
            } else {
                return ['status' => false, 'msg' => '注册失败。', 'error' => $userModel->getFirstErrors()];
            }
        } else {
            return ['status' => false, 'msg' => '注册失败。', 'error' => $userModel->getErrors()];
        }
    }

    public function actionGetAmount()
    {
        $userInfo = Yii::$app->user->identity;
        return ['status' => true, 'msg' => 'ok', 'data' => [
            'avatar' => Yii::$app->params['imgHost'].'/images/defaultAvatar.png',//头像
            'username' => $userInfo['username'],//用户名
            'amount' => 10000,//账户余额
            'principal' => 10,//本金
            'income' => 100//收益
        ]];


    }

}
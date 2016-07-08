<?php

namespace api\modules\v1\controllers;

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
                    'Access-Control-Request-Method' => ['POST','GET', 'PUT'],
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
     * "access_token": "xxxxxxxxxxxxxxxxxxxx"
     *
     * @return array
     */
    public function actionLogin()
    {
        $result = false;
        $accessToken = Yii::$app->request->get('access-token');
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
            return [
                'result' => '登录成功',
                'access-token' => $accessToken,
            ];
        } else {
            return [
                'result' => '登录失败',
            ];
        }
    }
public function actionLoginhost(){
    return ['status'=>true,'msg'=>'验证码发送成功,请查收手机短信。'];
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
        if($userModel->validate(['mobile'=>$mobile])){
            return ['status'=>true,'msg'=>'验证码发送成功,请查收手机短信。'];
        }else{
            return ['status'=>false,'msg'=>$userModel->getFirstError('mobile')];
        }
    }

}
<?php

namespace api\modules\v1\controllers;

use Yii;
use api\modules\v1\models\User;
use yii\rest\ActiveController;


class UserController extends ActiveController
{
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
                    $accessToken = $user->access_token ;
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

}
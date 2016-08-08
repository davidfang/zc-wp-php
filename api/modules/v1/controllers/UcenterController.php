<?php

namespace api\modules\v1\controllers;



use api\modules\v1\models\Transaction;
use Yii;
use yii\rest\ActiveController;

class UcenterController extends ActiveController
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
     * 获取用户账户数据
     * @return array
     */
    public function actionAmount()
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

    /**
     * 用户持仓头寸
     */
    public function actionPositions(){
        $userInfo = Yii::$app->user->identity;

        $positions = Transaction::getPositionsByUserId($userInfo['id']);
        return [
            'status'=> true,
            'msg'=> '请求成功',
            'data'=>[
                'positions'=>$positions
            ]
        ];
    }

}
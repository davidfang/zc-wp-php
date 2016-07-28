<?php
/**
* OrderController控制器
* Created by David
* User: David.Fang
* Date: 2016-7-22* Time: 15:17:59*/
namespace api\modules\v1\controllers;
use Yii;
use api\modules\v1\models\Transaction;

/**
 * 交易处理
 * Class TransactionController
 * @package api\modules\v1\controllers
 */
class TransactionController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Transaction';

    /**
     * 添加交易单
     */
    public function actionAdd(){
        $return = Yii::$app->params['return.false'];
        $transactionModel = new Transaction();
        $userInfo = Yii::$app->user->identity;
        $post = Yii::$app->request->post();
        $post['user_id'] = Yii::$app->user->identity->id;

        if ($transactionModel->load($post,'') && $transactionModel->validate()) {


            $price = 0;
            $redis = Yii::$app->redis;
            $post['goods_item'];
        $key = $stock.'-realTime-'.$time;
        //$result1 = $redis->executeCommand('ZREVRANGE',['zset-sliver-realTime-M1',0,30]);
        $result1 = $redis->executeCommand('ZREVRANGE',[$key.'-zset',0,30]);
        $post['price'] = $price;



            $transactionModel->save();
            $return['status'] = true;
            $return['msg'] = '交易成功';
            $return['data'] = ['id'=>$transactionModel->id];
        }else{
            $return['msg'] = '交易失败';
            $return['error'] = $transactionModel->getFirstErrors();
        }
        return $return ;
    }

    /**
     * 获得产品集合配置信息
     * @return mixed
     */
    public function actionGetGoodsItems()
    {
        $return = Yii::$app->params['return.true'];
        $return['data']['goods_items'] = Yii::$app->params['goods_items'];
        return $return ;
    }
}

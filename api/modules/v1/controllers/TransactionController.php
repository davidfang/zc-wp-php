<?php
/**
* OrderController控制器
* Created by David
* User: David.Fang
* Date: 2016-7-22* Time: 15:17:59*/
namespace api\modules\v1\controllers;
use api\modules\v1\models\Account;
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
        $transaction = Yii::$app->db->beginTransaction();
        $transactionModel = new Transaction();
        $userInfo = Yii::$app->user->identity;
        $post = Yii::$app->request->post();
        $post['user_id'] = Yii::$app->user->identity->id;

        try {
            if ($transactionModel->load($post, '') && $transactionModel->validate()) {

                //处理价格
                $price = 0;
                $redis = Yii::$app->redis;
                $goodsItem = $post['goods_item'];
                $goodsItemInfo = Yii::$app->params['goods_items'][$goodsItem];
                $key = $goodsItemInfo['symbol'];
                $currentPrice = array_combine($redis->hkeys($key . '-last'), $redis->hvals($key . '-last'));//当前价格
                $transactionConfig = Yii::$app->params['transaction.config'];//交易配置信息
                if ($post['type'] == 1) {//即时单  使用当前价格
                    $spreads = $transactionConfig['spreads'];//点差
                    $spreads = ($post['direction'] == 1) ? $spreads : (-$spreads);//根据方向选择正负点差
                    $post['price'] = $transactionModel->price = $currentPrice['open'] + $spreads;
                } else {//限价单  挂单交易
                    $speed = $transactionConfig['speed'];//限价单的限价范围
                    $speed = ($post['direction'] == 1) ? (-$speed) : $speed;//根据方向选择正负点差  买涨时限价更低  买跌时限价更高
                    $speedPrice = $currentPrice['open'] + $speed;
                    if ($post['direction'] == 1) {//买涨
                        if ($post['price'] > $speed) {//挂单买涨时，价格不能高于限制价格
                            $transactionModel->addError('price', '价格应<=' . $speedPrice);
                        }
                    } else {//买跌
                        if ($post['price'] < $speed) {//挂单买跌时，价格不能低于限制价格
                            $transactionModel->addError('price', '价格应>=' . $speedPrice);
                        }
                    }
                }
                //处理用户账户金额对比
                $userAmount = $redis->hget('user:' . $userInfo->id, 'amount');

                $transactionModel->amount = $transactionAmount = $transactionModel->price * $transactionModel->quantity * $goodsItemInfo['size'];//交易总金额 = 价格 X 数量 X 规格
                $transactionLever = $transactionConfig['lever'];//交易杠杆
                $transactionModel->use_funds = $transactionAmount / $transactionLever;//占用资金
                $userCanAmount = $userAmount * $transactionLever;//用户可使用金额
                $transactionSavingAmount = ($transactionConfig['saving'] * $goodsItemInfo['change'] * $transactionModel->quantity);//交易保全金额 = (交易保全变动值 X 变动金额 X 数量  )  //这里简单算它至少可以接受一次价格变动
                if ($transactionAmount > ($userCanAmount - $transactionSavingAmount)) {//交易需要金额  > (用户可使用金额-  交易保全金额)
                    $transactionCanQuantity = ($userAmount * $transactionLever) / (($transactionModel->price - $transactionConfig['saving']) * $goodsItemInfo['size']);//可交易最大数量 = （账户金额 X 杠杆）/ （(交易价格-交易保全变动值） X 规格）
                    $transactionModel->addError('quantity', '账户资金允许最大交易量为：' . $transactionCanQuantity);
                }
                //下面进行对比没错的情况，保存

                if (!$transactionModel->hasErrors()) {
                    $transactionModel->save();//写入交易记录
                    //从账户中扣除钱
                    $accountModel = Account::findOne(['user_id'=>$userInfo->id]);
                    $accountModel->freezingFunds($transactionModel->use_funds);
                    //var_dump($accountModel->getErrors());
                    $transaction->commit();
                    $return['status'] = true;
                    $return['msg'] = '交易成功';
                    $return['data'] = ['id' => $transactionModel->id];
                } else {
                    $return['msg'] = '交易失败';
                    $return['error'] = $transactionModel->getFirstErrors();
                }
            } else {
                $return['msg'] = '交易失败';
                $return['error'] = $transactionModel->getFirstErrors();
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            $return['msg'] = '交易失败';
            $return['error'] = ['异常错误'];
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

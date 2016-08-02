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
    public function actionAdd()
    {
        $return = Yii::$app->params['return.false'];
        $transaction = Yii::$app->db->beginTransaction();
        $transactionModel = new Transaction();
        $userInfo = Yii::$app->user->identity;
        $post = Yii::$app->request->post();
        $post['user_id'] = Yii::$app->user->identity->id;

        try {
            if ($transactionModel->load($post, '') && $transactionModel->validate()) {

                //  *处理价格*
                $price = 0;
                $redis = Yii::$app->redis;
                $goodsItem = $post['goods_item'];
                $goodsItemInfo = Yii::$app->params['goods_items'][$goodsItem];
                $key = $goodsItemInfo['symbol'];
                $currentPrice = array_combine($redis->hkeys($key . '-last'), $redis->hvals($key . '-last'));//当前价格
                $transactionModel->size = $goodsItemInfo['size'];
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
                $userAmount = $redis->hget('user:' . $userInfo->id, 'available_funds');//用户可用资金

                $transactionModel->amount = $transactionAmount = $transactionModel->price * $transactionModel->quantity * $goodsItemInfo['size'];//交易总金额 = 价格 X 数量 X 规格
                $transactionLever = $transactionConfig['lever'];//交易杠杆
                $transactionModel->use_funds = round($transactionAmount / $transactionLever,2);//占用资金
                $userCanAmount = $userAmount * $transactionLever;//用户可使用金额
                $transactionSavingAmount = ($transactionConfig['saving'] * $goodsItemInfo['change'] * $transactionModel->quantity);//交易保全金额 = (交易保全变动值 X 变动金额 X 数量  )  //这里简单算它至少可以接受一次价格变动
                if ($transactionAmount > ($userCanAmount - $transactionSavingAmount)) {//交易需要金额  > (用户可使用金额-  交易保全金额)
                    $transactionCanQuantity = ($userAmount * $transactionLever) / (($transactionModel->price - $transactionConfig['saving']) * $goodsItemInfo['size']);//可交易最大数量 = （账户金额 X 杠杆）/ （(交易价格-交易保全变动值） X 规格）
                    $transactionModel->addError('quantity', '账户资金允许最大交易量为：' . $transactionCanQuantity);
                }
                //*止损价格处理*
                if ($post['stop_loss'] != 0) {//有止损的情况
                    //剩余资金（1-此交订单占用资金 / 用户资金 )* 100 <  止损百分比
                    if ((1 - $transactionModel->use_funds / $userAmount) * 100 < $post['stop_loss']) {
                        $transactionModel->addError('stop_loss', '止损大于剩余可用资金');
                    }
                }
                // *下面进行对比,没错的情况，保存*

                if (!$transactionModel->hasErrors()) {
                    $stopLossPrice = $transactionModel->getStopLossPrice($userAmount);//计算止损价格
                    $stopProfitPrice = $transactionModel->getStopProfitPrice($userAmount);//计算止盈价格
                    //*写入交易记录*
                    $transactionModel->save();
                    //*从账户中扣除钱*
                    $accountModel = Account::findOne(['user_id' => $userInfo->id]);
                    $accountModel->freezingFunds($transactionModel->use_funds);
                    //var_dump($accountModel->getErrors());
                    $transaction->commit();//事务完成 写入REDIS事务
                    $redis->executeCommand('MULTI');//REDIS事务  开始
                    $redis->hincrbyfloat('user:'.$userInfo->id,'freezing_funds',$transactionModel->use_funds);//   增加冻结资金
                    $redis->hincrbyfloat('user:'.$userInfo->id,'available_funds',-$transactionModel->use_funds);//   减少可用资金
                    if ($transactionModel->stop_loss_price != 0) {//有止损  将止损写入redis 产品名：loss:[1,2] 止损价格  交易ID
                        $redis->zadd($goodsItemInfo['symbol'] . ':loss:' . $transactionModel->direction, $stopLossPrice, $transactionModel->id);
                    }

                    if ($transactionModel->stop_profit_price != 0) {//有止盈  将止盈写入redis 产品名：profit:[1,2] 止盈价格  交易ID
                        $redis->zadd($goodsItemInfo['symbol'] . ':profit:' . $transactionModel->direction, $stopProfitPrice, $transactionModel->id);
                    }
                    $redis->executeCommand('EXEC');//redis事务结束
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
        return $return;
    }


    /**
     * 获得产品集合配置信息
     * @return mixed
     */
    public function actionGetGoodsItems()
    {
        $return = Yii::$app->params['return.true'];
        $return['data']['goods_items'] = Yii::$app->params['goods_items'];
        return $return;
    }
}

<?php
/**
 * OrderController控制器
 * Created by David
 * User: David.Fang
 * Date: 2016-7-22* Time: 15:17:59*/
namespace api\modules\v1\controllers;

use api\modules\v1\models\Account;
use api\modules\v1\models\CashFlow;
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
                $currentPrice = array_combine($redis->hkeys($key . ':last'), $redis->hvals($key . ':last'));//当前价格
                $transactionModel->size = $goodsItemInfo['size'];
                $transactionConfig = Yii::$app->params['transaction.config'];//交易配置信息
                if ($post['type'] == 1) {//即时单  使用当前价格
                    $spreads = $transactionConfig['spreads'];//点差
                    $spreads = ($post['direction'] == 1) ? $spreads : (-$spreads);//根据方向选择正负点差
                    $post['price'] = $transactionModel->price = $currentPrice['open'] * 100 + $spreads;
                } else {//限价单  挂单交易
                    $speed = $transactionConfig['speed'];//限价单的限价范围
                    $speed = ($post['direction'] == 1) ? (-$speed) : $speed;//根据方向选择正负点差  买涨时限价更低  买跌时限价更高
                    $speedPrice = $currentPrice['open'] * 100 + $speed;
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
                $transactionModel->use_funds = floor($transactionAmount / $transactionLever * 100);//占用资金
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
                    //var_dump($accountModel);exit;
                    $transaction->commit();//事务完成 写入REDIS事务
                    $redis->executeCommand('MULTI');//REDIS事务  开始
                    $redis->hincrby('user:' . $userInfo->id, 'freezing_funds', $transactionModel->use_funds);//   增加冻结资金
                    $redis->hincrby('user:' . $userInfo->id, 'available_funds', -$transactionModel->use_funds);//   减少可用资金
                    if ($transactionModel->stop_loss_price != 0) {//有止损  将止损写入redis 产品名：loss:[1,2] 止损价格  交易ID
                        $redis->zadd($goodsItemInfo['symbol'] . ':loss:' . $transactionModel->direction, $stopLossPrice, $transactionModel->id);
                    }

                    if ($transactionModel->stop_profit_price != 0) {//有止盈  将止盈写入redis 产品名：profit:[1,2] 止盈价格  交易ID
                        $redis->zadd($goodsItemInfo['symbol'] . ':profit:' . $transactionModel->direction, $stopProfitPrice, $transactionModel->id);
                    }
                    //将交易信息全部写入redis供JS任务处理
                    $redis->hset('transaction', $transactionModel->id, json_encode($transactionModel->toArray()));
                    $redis->executeCommand('EXEC');//redis事务结束
                    $return['status'] = true;
                    $return['msg'] = '交易成功';
                    $return['data'] = ['id' => $transactionModel->id, 'use_found' => $transactionModel->use_funds, '$transactionModel->amount' => $transactionModel->amount, '$transactionAmount' => $transactionAmount];
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

    //平仓

    public function actionClose($id)
    {
        $redis = Yii::$app->redis;

        //1. 根据交易单号，从Redis取出交易信息；
        $transactionStr = $redis->hget('transaction', $id);
        //var_dump($transactionStr);exit;
        if (!is_null($transactionStr)) {//交易单还处理生效状态
            $transactionInfoArray = json_decode($transactionStr, true);
            if ($transactionInfoArray['user_id'] == Yii::$app->user->id) {

                $transaction = Yii::$app->db->beginTransaction();//mysql事务 开始
                $transactionModel = Transaction::findOne($transactionInfoArray['id']);
                //2. 删除交易单中的止损止盈信息；
                $goodsItemInfo = Yii::$app->params['goods_items'][$transactionInfoArray['goods_item']];//此次订单的产品ID对应产品配置信息
                $transactionConfig = Yii::$app->params['transaction.config'];//交易配置信息
                $symbol = $goodsItemInfo['symbol'];
                $currentPrice = array_combine($redis->hkeys($symbol . ':last'), $redis->hvals($symbol . ':last'));//当前价格
                if ($transactionInfoArray['stop_loss_price'] != 0) {//有止损 删除止损信息
                    $redis->zrem($symbol . ':loss:' . $transactionInfoArray['direction'], $transactionInfoArray['id']);
                }
                if ($transactionInfoArray['stop_profit_price'] != 0) {//有止盈 删除止盈信息
                    $redis->zrem($symbol . ':profit:' . $transactionInfoArray['direction'], $transactionInfoArray['id']);
                }
                //3. 修改交易单号状态为关闭（订单已结束2）；
                $transactionModel->status = '2';
                //4. 计算盈亏，关闭类型修改为人为关闭，更新关闭时间、关闭价格（当前价格）、盈亏；
                //计算盈亏，
                $profitLoss = ($currentPrice['open'] * 100 - $transactionModel->price) / $transactionConfig['basicPoint'] * $goodsItemInfo['change']; //盈亏 = （当前价格 - 交易价格）/ 交易基点  X 交易产品变动值
                if ($transactionModel->direction != 1) {//买跌，不是买涨，
                    $profitLoss = -$profitLoss;//买跌时盈亏计算取反
                }
                $transactionModel->profit_loss = $profitLoss;

                $transactionModel->close_type = '1';//关闭类型修改为人为关闭

                $transactionModel->close_at = time();
                $transactionModel->close_price = $currentPrice['open'] * 100;
                $transactionModel->save();
                //$transactionModel->touch('close_at');
                //$transactionModel->save();
                //5. 删除Redis中transaction中交易单号信息；
                $redis->executeCommand('MULTI');//REDIS事务  开始
                $redis->hdel('transaction', $transactionModel->id);
                //6. 解冻资金，还原可用资金，将盈亏记入资金流水，修改资金总额、可用资金
                $accountModel = Account::findOne(['user_id' => $transactionModel->user_id]);
                //解冻资金
                $redis->hincrby('user:' . $transactionModel->user_id, 'freezing_funds', -$transactionModel->use_funds);
                $accountModel->freezing_funds -= $transactionModel->use_funds;
                //还原可用资金
                $redis->hincrby('user:' . $transactionModel->user_id, 'available_funds', $transactionModel->use_funds);
                $accountModel->available_funds += $transactionModel->use_funds;

                //将盈亏记入资金流水，
                $cashFlowModel = new CashFlow();
                $cashFlowModel->money = $profitLoss;
                $cashFlowModel->type = '2';
                $cashFlowModel->user_id = $transactionModel->user_id;
                $cashFlowModel->transaction_id = $transactionModel->id;
                $cashFlowModel->remark = date('Y-m-d H:i:s') . $goodsItemInfo['name'] . $goodsItemInfo['size'] . $goodsItemInfo['unit'] . ' ' . $transactionModel->quantity . ' ' . $profitLoss . '元';//备注格式：2016-8-1 12：30：22 白银100g 5手  亏-/盈 100元
                $cashFlowModel->save();
                //修改资金总额、可用资金
                $accountModel->account += $profitLoss;
                $accountModel->available_funds += $profitLoss;
                $accountModel->save();

                //7. 修改Redis中user信息：资金总额，可用资金
                $redis->hincrby('user:' . $transactionModel->user_id, 'amount', $profitLoss);//   增加资金总额
                $redis->hincrby('user:' . $transactionModel->user_id, 'available_funds', $profitLoss);//   增加可用资金
                //8. 发微信给用户，告知用户订单关闭信息；
                $redis->executeCommand('EXEC');//redis事务结束
                $transaction->commit();//mysql事务结束
                $return = array_merge(Yii::$app->params['return.true'], ['data' => ['cash_flow_id' => $cashFlowModel->id, 'currentPrice' => $currentPrice]]);

            } else {
                $return = array_merge(Yii::$app->params['return.false'], ['error' => ['无权平仓别人的交易']]);
            }
        } else {//交易单不存在或者已经被平仓
            $return = array_merge(Yii::$app->params['return.false'], ['error' => ['请检查交易单号']]);
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

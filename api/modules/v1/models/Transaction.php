<?php

namespace api\modules\v1\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * "zc_order"表的model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $goods_item
 * @property integer $size
 * @property string $type
 * @property string $direction
 * @property double $price
 * @property integer $quantity
 * @property integer $amount
 * @property integer $use_funds
 * @property integer $stop_loss
 * @property integer $stop_loss_price
 * @property integer $stop_profit
 * @property integer $stop_profit_price
 * @property string $status
 * @property string $close_type
 * @property integer $created_at
 * @property integer $close_at
 * @property double $close_price
 * @property double $profit_loss
 * @property integer $updated_at
 */
class Transaction extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%transaction}}';
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
            [['user_id', 'goods_item', 'quantity'], 'required'],
            ['price', 'required', 'when' => function ($model) {//限价单时，必须有价格
                return $model->type == 2;
            }],
            [['user_id', 'created_at', 'close_at', 'updated_at', 'stop_loss', 'stop_profit','goods_item', 'size', 'quantity', 'amount', 'use_funds', 'stop_loss_price', 'stop_profit_price','price','close_price','profit_loss'], 'integer'],
            ['type', 'in', 'range' => Yii::$app->params['transaction.rang']['type']],
            ['direction', 'in', 'range' => Yii::$app->params['transaction.rang']['direction']],
            ['status', 'in', 'range' => Yii::$app->params['transaction.rang']['status']],
            ['close_type', 'in', 'range' => Yii::$app->params['transaction.rang']['close_type']],
            [['user_id', 'goods_item', 'quantity', 'created_at', 'close_at', 'updated_at'], 'compare', 'compareValue' => 0, 'operator' => '>'],
            [['stop_loss', 'stop_profit'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['type', 'direction', 'status', 'close_type'], 'string']
        ];
    }

    /**
     * 设置列表页显示列和搜索列
     * @inheritdoc
     */
    public function getIndexLists()
    {
        return [
            'id',// 'ID',
            'user_id',// '用户ID',
            'goods_item',// '产品编码',
            'size',// '产品规格',
            'type',// '类型：1即时单，2限价单',
            'direction',// '方向：1买涨2买跌',
            'price',// '价格：即时单为当前方向的最新价，限价单为用户设置价',
            'quantity',// '数量',
            'amount',//金额
            'use_funds',//占用资金
            'stop_loss',// '止损百分比',
            'stop_loss_price',// '止损价格',
            'stop_profit',// '止盈百分比',
            'stop_profit_price',// '止盈价格',
            'status',// '状态：0限价单还未生效，1已生效订单，2已结束订单',
            'close_type',// '关闭类型：0未关闭，1用户人为关闭，2止损止盈触发关闭，3爆仓关闭',
            'created_at',// '创建时间',
            'close_at',// '关闭时间',
            'close_price',// '关闭价格',
            'profit_loss',// '盈亏',
            'updated_at',// '结束时间',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'goods_item' => '产品编码',
            'size' => '产品规格',
            'type' => '类型：1即时单，2限价单',
            'direction' => '方向：1买涨2买跌',
            'price' => '价格：即时单为当前方向的最新价，限价单为用户设置价',
            'quantity' => '数量',
            'amount' => '金额',
            'use_funds' => '占用资金',
            'stop_loss' => '止损百分比',
            'stop_loss_price' => '止损价格',
            'stop_profit' => '止盈百分比',
            'stop_profit_price' => '止盈价格',
            'status' => '状态：0限价单还未生效，1已生效订单，2已结束订单',
            'close_type' => '关闭类型：0未关闭，1用户人为关闭，2止损触发关闭，3止盈触发关闭，4爆仓关闭',
            'created_at' => '创建时间',
            'close_at' => '关闭时间',
            'close_price' => '关闭价格',
            'profit_loss' => '盈亏',
            'updated_at' => '结束时间',
        ];
    }

    /**
     * 多选项配置
     * @return array
     */
    public function getOptions()
    {
        return [
            'type' => [
                1 => '1',
                2 => '2',
            ],
            'direction' => [
                1 => '1',
                2 => '2',
            ],
            'status' => [
                '0',
                '1',
                '2',
            ],
            'close_type' => [
                '0',
                '1',
                '2',
                '3',
                '4'
            ],
        ];
    }

    /**
     * toolbars工具栏按钮设定
     * 字段为枚举类型时存在
     * 默认为复选项的值，
     * jsfunction默认值为changeStatus
     * @return array
     * 返回值举例：
     * [
     *  ['name'=>'忘却',//名称
     *  'jsfunction'=>'ask',//js操作方法，默认为：changeStatus
     *  'field'=>'status_2',//操作字段名
     *  'field_value'=>'3'],//修改后的值
     *  ]
     */
    public function getToolbars()
    {
        $attributeLabels = $this->attributeLabels();
        $options = $this->options;
        return [
            [
                'name' => $options["type"]["1"],
                'jsfunction' => 'changeStatus',
                'field' => 'type',
                'field_value' => '1'
            ],
            [
                'name' => $options["type"]["2"],
                'jsfunction' => 'changeStatus',
                'field' => 'type',
                'field_value' => '2'
            ],
            [
                'name' => $options["direction"]["1"],
                'jsfunction' => 'changeStatus',
                'field' => 'direction',
                'field_value' => '1'
            ],
            [
                'name' => $options["direction"]["2"],
                'jsfunction' => 'changeStatus',
                'field' => 'direction',
                'field_value' => '2'
            ],
            [
                'name' => $options["status"]["0"],
                'jsfunction' => 'changeStatus',
                'field' => 'status',
                'field_value' => '0'
            ],
            [
                'name' => $options["status"]["1"],
                'jsfunction' => 'changeStatus',
                'field' => 'status',
                'field_value' => '1'
            ],
            [
                'name' => $options["status"]["2"],
                'jsfunction' => 'changeStatus',
                'field' => 'status',
                'field_value' => '2'
            ],
            [
                'name' => $options["close_type"]["0"],
                'jsfunction' => 'changeStatus',
                'field' => 'close_type',
                'field_value' => '0'
            ],
            [
                'name' => $options["close_type"]["1"],
                'jsfunction' => 'changeStatus',
                'field' => 'close_type',
                'field_value' => '1'
            ],
            [
                'name' => $options["close_type"]["2"],
                'jsfunction' => 'changeStatus',
                'field' => 'close_type',
                'field_value' => '2'
            ],
            [
                'name' => $options["close_type"]["3"],
                'jsfunction' => 'changeStatus',
                'field' => 'close_type',
                'field_value' => '3'
            ],
            [
                'name' => $options["close_type"]["4"],
                'jsfunction' => 'changeStatus',
                'field' => 'close_type',
                'field_value' => '4'
            ],
        ];
    }

    /**
     * 计算止损价格
     * @param $total  总资金
     * @return float
     */
    public function getStopLossPrice($total)
    {

        if ($this->stop_loss != 0) {//用户有主动设置止损百分比
            $transactionConfig = Yii::$app->params['transaction.config'];//交易配置信息
            $spreads = $transactionConfig['spreads']; //交易点差

            $lossInterval = $this->amount * $this->stop_loss / 100 / ($spreads * $this->quantity * $this->size) ;//止损区间点数=总价 X 止损百分比 /(交易点差 X  交易量 X 交易规格）

            if ($this->direction == 1) {//买涨
                $this->stop_loss_price = $this->price - floor($lossInterval);//开始价格-止损点数 X 交易基点
            } else {
                $this->stop_loss_price = $this->price + floor($lossInterval);//
            }

        } else {//用户没有设置止损
            $this->stop_loss_price =  0;
        }

        return $this->stop_loss_price;
    }

    /**
     * 计算止盈价格
     * @param $total  总资金
     * @return float
     */
    public function getStopProfitPrice($total)
    {
        if ($this->stop_profit != 0) {
            $transactionConfig = Yii::$app->params['transaction.config'];//交易配置信息
            $spreads = $transactionConfig['spreads']; //交易点差

            $profitInterval = $this->amount * $this->stop_profit / 100 / ($spreads * $this->quantity * $this->size)  ;//止盈区间点数=总价 X 止盈百分比 /(交易点差 X  交易量 X 交易规格）
            if ($this->direction == 1) {//买涨
                $this->stop_profit_price = $this->price + floor($profitInterval);//开始价格-止盈点数 X 交易基点
            } else {
                $this->stop_profit_price = $this->price - floor($profitInterval);//
            }
        } else {
            $this->stop_profit_price = 0;
        }
        return $this->stop_profit_price;
    }

    /**
     * 根据用户ID获得用户持仓头寸
     * @param $userId
     * @return $this
     */
    public static function getPositionsByUserId($userId)
    {
        return self::find()->where(['user_id'=>$userId,'status'=>'1'])->orderBy(['id'=>'desc'])->asArray()->all();
        //return self::find(['user_id'=>$userId,'status'=>'1'])->select(['id','user_id','goods_item','size','type','direction','price','quantity','amount'])->orderBy(['id'=>'desc'])->asArray()->all();
    }

    /**
     * 关闭持仓（平仓）
     * @param $transactionInfoArray  交易信息数组
     * @return CashFlow
     */
    public static function close($transactionInfoArray){
        $redis = Yii::$app->redis;

        $transaction = Yii::$app->db->beginTransaction();//mysql事务 开始
        $transactionModel = self::findOne($transactionInfoArray['id']);
        //2. 删除交易单中的止损止盈信息；
        $goodsItemInfo = Yii::$app->params['goods_items'][$transactionInfoArray['goods_item']];//此次订单的产品ID对应产品配置信息
        $transactionConfig = Yii::$app->params['transaction.config'];//交易配置信息
        $symbol = $goodsItemInfo['symbol'];
        $currentPrice = $transactionInfoArray['close_price'];//当前价格
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
        $profitLoss = ($currentPrice - $transactionModel->price)  * $goodsItemInfo['change'] * $transactionModel->quantity; //盈亏 = （当前取整价格 - 交易价格） X 交易产品变动值 X 订单数量
        if ($transactionModel->direction != 1) {//买跌，不是买涨，
            $profitLoss = -$profitLoss;//买跌时盈亏计算取反
        }
        $transactionModel->profit_loss = $profitLoss;//盈亏

        $transactionModel->close_type = $transactionInfoArray['close_type'];//关闭类型

        $transactionModel->close_at = $transactionInfoArray['close_at'];//关闭时间
        $transactionModel->close_price = $transactionInfoArray['close_price'];//关闭价格
        $transactionModel->save();
        //$transactionModel->touch('close_at');
        //$transactionModel->save();
        //5. 删除Redis中transaction中交易单号信息；
        $redis->executeCommand('MULTI');//REDIS事务  开始
        $redis->hdel('transaction', $transactionModel->id);//删除交易表中对应交易单的交易信息
        $redis->hdel('transaction:'.$transactionInfoArray['user_id'], $transactionModel->id);//删除用户交易表中对应交易单的交易信息
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
        $redis->hincrby('user:' . $transactionModel->user_id, 'account', $profitLoss);//   增加资金总额
        $redis->hincrby('user:' . $transactionModel->user_id, 'available_funds', $profitLoss);//   增加可用资金
        //8. 发微信给用户，告知用户订单关闭信息；
        $redis->executeCommand('EXEC');//redis事务结束
        $transaction->commit();//mysql事务结束
        return $cashFlowModel;
    }
}

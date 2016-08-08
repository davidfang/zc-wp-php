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

            $lossInterval = $total * $this->stop_loss / 100 / ($spreads * $this->quantity * $this->size) * 100;//止损区间点数=总价 X 止损百分比 /(交易点差 X  交易量 X 交易规格）X 100（取整处理）

            if ($this->direction == 1) {//买涨
                $this->stop_loss_price = $this->price - $lossInterval * $transactionConfig['basicPoint'];//开始价格-止损点数 X 交易基点
            } else {
                $this->stop_loss_price = $this->price + $lossInterval * $transactionConfig['basicPoint'];//
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

            $profitInterval = $total * $this->stop_profit / 100 / ($spreads * $this->quantity * $this->size) * 100 ;//止盈区间点数=总价 X 止盈百分比 /(交易点差 X  交易量 X 交易规格）X 100（取整处理）
            if ($this->direction == 1) {//买涨
                $this->stop_profit_price = $this->price + $profitInterval * $transactionConfig['basicPoint'];//开始价格-止盈点数 X 交易基点
            } else {
                $this->stop_profit_price = $this->price - $profitInterval * $transactionConfig['basicPoint'];//
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
}

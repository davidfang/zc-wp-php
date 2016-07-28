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
 * @property string $type
 * @property string $direction
 * @property double $price
 * @property integer $quantity
 * @property integer $stop
 * @property string $status
 * @property string $close_type
 * @property integer $created_at
 * @property integer $close_at
 * @property double $close_price
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
            [ 'price', 'required','when'=>function($model){
                return $model->type == 2;
            }],
            [['user_id', 'created_at', 'close_at', 'updated_at'], 'integer'],
            [['goods_item', 'quantity', 'stop', ], 'number'],
            ['type', 'in', 'range' =>Yii::$app->params['transaction.rang']['type'] ],
            ['direction', 'in', 'range' =>Yii::$app->params['transaction.rang']['direction']],
            ['status', 'in', 'range' =>Yii::$app->params['transaction.rang']['status']],
            ['close_type', 'in', 'range' =>Yii::$app->params['transaction.rang']['close_type'] ],
            [['user_id','goods_item', 'quantity', 'stop', 'created_at', 'close_at', 'updated_at'], 'compare', 'compareValue' => 0, 'operator' => '>'],
            [['type', 'direction', 'status', 'close_type'], 'string'],
            [['price', 'close_price'], 'number']
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
            'type',// '类型：1即时单，2限价单',
            'direction',// '方向：1买涨2买跌',
            'price',// '价格：即时单为当前方向的最新价，限价单为用户设置价',
            'quantity',// '数量',
            'stop',// '止损止盈价格',
            'status',// '状态：0限价单还未生效，1已生效订单，2已结束订单',
            'close_type',// '关闭类型：0未关闭，1用户人为关闭，2止损止盈触发关闭，3爆仓关闭',
            'created_at',// '创建时间',
            'close_at',// '关闭时间',
            'close_price',// '关闭价格',
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
            'type' => '类型：1即时单，2限价单',
            'direction' => '方向：1买涨2买跌',
            'price' => '价格：即时单为当前方向的最新价，限价单为用户设置价',
            'quantity' => '数量',
            'stop' => '止损止盈价格',
            'status' => '状态：0限价单还未生效，1已生效订单，2已结束订单',
            'close_type' => '关闭类型：0未关闭，1用户人为关闭，2止损止盈触发关闭，3爆仓关闭',
            'created_at' => '创建时间',
            'close_at' => '关闭时间',
            'close_price' => '关闭价格',
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
                'name'=>$options["type"]["1"],
                'jsfunction'=>'changeStatus',
                'field'=>'type',
                'field_value'=>'1'
                ],
                [
                'name'=>$options["type"]["2"],
                'jsfunction'=>'changeStatus',
                'field'=>'type',
                'field_value'=>'2'
                ],
                [
                'name'=>$options["direction"]["1"],
                'jsfunction'=>'changeStatus',
                'field'=>'direction',
                'field_value'=>'1'
                ],
                [
                'name'=>$options["direction"]["2"],
                'jsfunction'=>'changeStatus',
                'field'=>'direction',
                'field_value'=>'2'
                ],
                [
                'name'=>$options["status"]["0"],
                'jsfunction'=>'changeStatus',
                'field'=>'status',
                'field_value'=>'0'
                ],
                [
                'name'=>$options["status"]["1"],
                'jsfunction'=>'changeStatus',
                'field'=>'status',
                'field_value'=>'1'
                ],
                [
                'name'=>$options["status"]["2"],
                'jsfunction'=>'changeStatus',
                'field'=>'status',
                'field_value'=>'2'
                ],
                [
                'name'=>$options["close_type"]["0"],
                'jsfunction'=>'changeStatus',
                'field'=>'close_type',
                'field_value'=>'0'
                ],
                [
                'name'=>$options["close_type"]["1"],
                'jsfunction'=>'changeStatus',
                'field'=>'close_type',
                'field_value'=>'1'
                ],
                [
                'name'=>$options["close_type"]["2"],
                'jsfunction'=>'changeStatus',
                'field'=>'close_type',
                'field_value'=>'2'
                ],
        ];
    }

}

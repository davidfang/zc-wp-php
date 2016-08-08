<?php

namespace api\modules\v1\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * "zc_cash_flow"表的model
 *
 * @property integer $id
 * @property double $money
 * @property string $type
 * @property string $user_id
 * @property string $weixin_order_id
 * @property integer $transaction_id
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 */
class CashFlow extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cash_flow}}';
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
            [['money','user_id'], 'required'],
            [['type'], 'string'],
            [['money','transaction_id','user_id', 'created_at', 'updated_at'], 'integer'],
            [['weixin_order_id', 'remark'], 'string', 'max' => 100]
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
            'money',// '金额',
            'type',// '资金类型：1入金，2交易盈亏，3出金，4其它',
            'user_id',//用户ID
            'weixin_order_id',// '微信交易单ID',
            'transaction_id',// '交易订单ID',
            'remark',// '备注',
            'created_at',// '创建时间',
            'updated_at',// '修改时间',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'money' => '金额',
            'type' => '资金类型：1入金，2交易盈亏，3出金，4其它',
            'user_id' => '用户ID',
            'weixin_order_id' => '微信交易单ID',
            'transaction_id' => '交易订单ID',
            'remark' => '备注',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
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
        3 => '3',
        4 => '4',
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
                'name'=>$options["type"]["3"],
                'jsfunction'=>'changeStatus',
                'field'=>'type',
                'field_value'=>'3'
                ],
                [
                'name'=>$options["type"]["4"],
                'jsfunction'=>'changeStatus',
                'field'=>'type',
                'field_value'=>'4'
                ],
        ];
    }

}

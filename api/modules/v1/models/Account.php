<?php

namespace api\modules\v1\models;

use Yii;

/**
 * "zc_account"表的model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $account
 * @property integer $in
 * @property integer $out
 * @property integer $created_at
 * @property integer $updated_at
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%account}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'account', 'in', 'out', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'account', 'in', 'out', 'created_at', 'updated_at'], 'integer']
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
            'account',// '用户账户金额',
            'in',// '入金',
            'out',// '出金',
            'created_at',// '创建时间',
            'updated_at',// '更新时间',
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
            'account' => '用户账户金额',
            'in' => '入金',
            'out' => '出金',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 多选项配置
     * @return array
     */
    public function getOptions()
    {
          return [];
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
            ];
    }

}

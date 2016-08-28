<?php

namespace api\modules\v1\models;

use Yii;

/**
 * "zc_account"表的model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $account
 * @property integer $freezing_funds
 * @property integer $available_funds
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
            [['user_id'], 'required'],
            ['user_id', 'unique'],
            [['user_id', 'account', 'in', 'out', 'freezing_funds', 'available_funds', 'created_at', 'updated_at'], 'integer']
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
            'freezing_funds',// '冻结资金',
            'available_funds',// '可用资金',
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
            'freezing_funds' => '冻结资金',
            'available_funds' => '可用资金',
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
    /**生成原始帐户
     * @param $user_id
     * @return $this|bool
     */
    function generatorAccount($user_id){
        $this->user_id = $user_id;
        $this->account = 1000000;
        $this->freezing_funds = 0;
        $this->available_funds = 1000000;

        if($this->validate()){
            $this->save();
            $redis = Yii::$app->redis;
            //写入redis
            $redis->hmset('user:'.$user_id,'account',$this->account,'freezing_funds',$this->freezing_funds,'available_funds',$this->available_funds);
            return $this;
        }else{
            return false;
        }
}
    /**
     * 根据用户ID获得用户的账户信息
     * @param $user_id
     * @return \yii\db\ActiveQuery
     */
public function getAccountByUserId($user_id){
    return self::find(['user_id'=>$user_id]);
}
    /**
     * 冻结资金
     * @param $amount
     */
    public function freezingFunds($amount){
        //var_dump([ $this->freezing_funds,$this->available_funds,$amount]);
        $this->freezing_funds +=   $amount;
        $this->available_funds -=   $amount;
        //var_dump([ $this->freezing_funds,$this->available_funds]);//exit;
        $this->save();
    }
/**
     * 解冻资金
     * @param $amount
     */
    public function unFreezingFunds($amount){
        $this->freezing_funds = $this->freezing_funds - $amount;
        $this->available_funds = $this->available_funds + $amount;
        $this->save();
    }

}

<?php

namespace api\modules\v1\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * "zc_mobile_verification"表的model
 *
 * @property integer $id
 * @property string $mobile
 * @property string $code
 * @property integer $create_at
 * @property integer $update_at
 */
class MobileVerification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mobile_verification}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::className(),]
            //'value' => new Expression('NOW()'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'code'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['mobile'], 'string', 'max' => 11],
            [['code'], 'string', 'max' => 4]
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
            'mobile',// '手机号',
            'code',// '验证码',
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
            'mobile' => '手机号',
            'code' => '验证码',
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

    /**
     * 生成手机验证码
     * @param $mobile
     * @return int|mixed|string
     */
    public static function generator($mobile)
    {
        $check = self::checkCode($mobile);
        if ($check) {
            return $check->code;
        } else {
            $mobileVerification = new MobileVerification();
            $mobileVerification->mobile = $mobile;
            $mobileVerification->code = $code = '' . rand(1000, 9999);
            if ($mobileVerification->save()) {
                return $code;
            } else {
                return '';
            }
        }
    }

    /**
     * 检查验证码
     * 不填验证码的时候返回手机的最新验证码信息
     * @param $mobile 手机
     * @param string $code 验证码
     * @return array|bool|null|\yii\db\ActiveRecord
     */
    public static function checkCode($mobile, $code = '')
    {
        $info = MobileVerification::find()->where(['mobile' => $mobile])->andWhere(['>', 'created_at', strtotime('-5 minutes')])->one();
        if ($info) {
            if ($code == '') {
                return $info;
            } else {
                return $code == $info->code;
            }
        } else {
            return false;
        }
    }
}

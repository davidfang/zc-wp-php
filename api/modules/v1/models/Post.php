<?php

namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\Link;
use yii\web\Linkable;
use yii\helpers\Url;
/**
 * "xw_post"表的model
 *
 * @property string $id
 * @property string $name
 * @property string $sort
 * @property string $image
 * @property string $jump_url
 * @property string $jump_type_r
 * @property string $status
 * @property string $start_date
 * @property string $end_date
 * @property string $date_type_r
 */
class Post extends ActiveRecord implements Linkable
{
    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(['post/view', 'id' => $this->id], true),
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%post}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'sort'], 'integer'],
            [['jump_type_r', 'status', 'date_type_r'], 'string'],
            [['start_date', 'end_date'], 'safe'],
            [['name', 'jump_url'], 'string', 'max' => 30],
            [['image'], 'string', 'max' => 255]
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
            'name',// '广告位名称',
            'sort',// '显示排序',
            'image',// '广告图片',
            'jump_url',// '跳转地址',
            'jump_type_r',// '跳转类型 0 自定义 1 单品页',
            'status',// '状态，1 停止 2 暂停 3 删除 4启用',
            'start_date',// '开始时间',
            'end_date',// '结束时间',
            'date_type_r',// '时间类型 0 使用已定义时间 1 不限时间',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '广告位名称',
            'sort' => '显示排序',
            'image' => '广告图片',
            'jump_url' => '跳转地址',
            'jump_type_r' => '跳转类型 0 自定义 1 单品页',
            'status' => '状态，1 停止 2 暂停 3 删除 4启用',
            'start_date' => '开始时间',
            'end_date' => '结束时间',
            'date_type_r' => '时间类型 0 使用已定义时间 1 不限时间',
        ];
    }

    /**
     * 多选项配置
     * @return array
     */
    public function getOptions()
    {
        return [
            'jump_type_r' => [
                '0'=>'自定义',
                '1'=>'单品页',
            ],
            'status' => [
                1 => '停止',
                2 => '暂停',
                3 => '删除',
                4 => '启动',
            ],
            'date_type_r' => [
                '0'=>'限时',
                '1'=>'不限',
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
     * ['name'=>'忘却',//名称
     * 'jsfunction'=>'ask',//js操作方法，默认为：changeStatus
     * 'field'=>'status_2',//操作字段名
     * 'field_value'=>'3'],//修改后的值
     * ]
     */
    public function getToolbars()
    {
        $attributeLabels = $this->attributeLabels();
        $options = $this->options;
        return [
            [
                'name' => $options["jump_type_r"]["0"],
                'jsfunction' => 'changeStatus',
                'field' => 'jump_type_r',
                'field_value' => '0'
            ],
            [
                'name' => $options["jump_type_r"]["1"],
                'jsfunction' => 'changeStatus',
                'field' => 'jump_type_r',
                'field_value' => '1'
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
                'name' => $options["status"]["3"],
                'jsfunction' => 'changeStatus',
                'field' => 'status',
                'field_value' => '3'
            ],
            [
                'name' => $options["status"]["4"],
                'jsfunction' => 'changeStatus',
                'field' => 'status',
                'field_value' => '4'
            ],
            [
                'name' => $options["date_type_r"]["0"],
                'jsfunction' => 'changeStatus',
                'field' => 'date_type_r',
                'field_value' => '0'
            ],
            [
                'name' => $options["date_type_r"]["1"],
                'jsfunction' => 'changeStatus',
                'field' => 'date_type_r',
                'field_value' => '1'
            ],
        ];
    }

}

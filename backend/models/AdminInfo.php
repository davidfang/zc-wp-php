<?php

namespace app\models;

use app\models\AdminUser;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%admin_info}}".
 *
 * @property integer $id
 * @property string $city
 * @property string $department
 * @property string $status
 * @property string $in_time
 * @property string $id_number
 * @property string $sex
 * @property string $birthday
 * @property integer $birthday_month
 * @property integer $age
 * @property string $mobile
 * @property integer $created_at
 * @property integer $updated_at
 */
class AdminInfo extends \yii\db\ActiveRecord
{
    public $username;
    public $email;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_info}}';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            ['class' => TimestampBehavior::className(),
                //'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => false
            ]
            //'value' => new Expression('NOW()'),
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'birthday_month', 'age', 'created_at', 'updated_at'], 'integer'],
            [['status', 'sex'], 'string'],
            [['city', 'department'], 'string', 'max' => 255],
            [['in_time', 'birthday'], 'string', 'max' => 10],
            [['id_number'], 'string', 'max' => 18],
            [['mobile'], 'string', 'max' => 15]
        ];
    }
    public function getUser(){
        return $this->hasOne(AdminUser::className(), ['id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '用户ID',
            'city' => '城市',
            'department' => '部门',
            'status' => '状态',
            'in_time' => '入职时间',
            'id_number' => '身份证号',
            'sex' => '性别',
            'birthday' => '生日',
            'birthday_month' => '生日月份',
            'age' => '年龄',
            'mobile' => '联系电话',
            'created_at' => '建立时间',
            'updated_at' => '更新时间',
        ];
    }
}

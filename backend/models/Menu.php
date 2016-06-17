<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "t_menu".
 *
 * @property integer $id
 * @property string $menuname
 * @property integer $parentid
 * @property string $route
 * @property string $menuicon
 * @property integer $level
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menuname','route'], 'required'],
            ['route','unique'],
            [['parentid', 'level'], 'integer'],
            [['menuname', 'route'], 'string', 'max' => 32],
            [['menuicon'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'menuname' => '菜单名称',
            'parentid' => '父类ID',
            'route' => '路由',
            'menuicon' => '图标',
            'level' => '级别',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $auth = Yii::$app->authManager;
        if($insert)
        {
            $permission = $auth->createPermission($this->menuname);
            $permission->description = $this->route;
            $auth->add($permission);
        }else
        {
            $route = ArrayHelper::getValue($changedAttributes,'menuname',$this->menuname);
            $permission = $auth->getPermission($route);
            $permission->name = $this->menuname;
            $permission->description = $this->route;
            $auth->update($route,$permission);
        }

    }

    public function afterDelete()
    {
        parent::afterDelete();
        //删除所有权限
        $auth = Yii::$app->authManager;
        if($p = $auth->getPermission($this->menuname))
            $auth->remove($p);
    }
    /**
     * 获取子菜单
     * @return static
     */
    public function getSon()
    {
        return $this->hasMany(Menu::className(),['parentid'=>'id'])->orderBy('level desc');
    }
    /**
     * 获取父菜单
     */
    public function getFather()
    {
        return $this->hasOne(Menu::className(),['id'=>'parentid']);
    }

    /**
     * 生成菜单
     * @return string
     */
    public static function generateMenuByUser()
    {
        $list = Menu::find()->where('level=1')->all();
        $menu = Yii::$app->controller->renderPartial('@backend/views/home/_menu',[
            'list'=>$list,
            'admin'=>(Yii::$app->user->id==1)?true:false
        ]);
        return $menu;
    }
}

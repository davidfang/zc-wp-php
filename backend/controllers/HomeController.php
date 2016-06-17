<?php

namespace backend\controllers;

use Yii;
use yii\caching\ChainedDependency;
use yii\caching\ExpressionDependency;
use yii\caching\DbDependency;
use app\models\Menu;
use yii\web\Controller;
use yii\filters\AccessControl;
//use zc\rbac\components\AccessControl;

class HomeController extends Controller
{

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction'
            ],
        ];
    }

    public function actionIndex()
    {
        //缓存一个带有依赖的缓存
        $key = '_menu' . Yii::$app->user->id;
        if (Yii::$app->session->getFlash('reflush') || !Yii::$app->cache->get($key)) {
            //如果缓存依赖发生改变，重新生成缓存
            $dp = new ExpressionDependency([
                'expression' => 'count(Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->id))'
            ]);
            $authManager = new \yii\rbac\DbManager();
            $dp2 = new DbDependency([
                'sql' => "select max(updated_at) from ".$authManager->itemTable,//"{{%auth_item}}",
            ]);
            Yii::$app->cache->set($key, 'nothing', 0, new ChainedDependency([
                'dependencies' => [$dp, $dp2]
            ]));
            //利用上面的缓存依赖生成菜单的永久缓存
            $_list = Menu::generateMenuByUser();
            Yii::$app->cache->set('menulist-' . Yii::$app->user->id, $_list, 0);
        }
        return $this->render('index');
    }
}
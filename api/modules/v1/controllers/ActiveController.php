<?php
/**
* PostController控制器
* Created by David
* User: David.Fang
* Date: 2016-1-22* Time: 18:57:59*/
namespace api\modules\v1\controllers;

use yii\rest\ActiveController as RestActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use api\common\QueryParamAuth;
use zc\rbac\components\AccessControl;
use yii\filters\ApiAccessControl;


class ActiveController extends RestActiveController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        /*$behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];*/
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            /*'allowActions' => [
                '/',
                '/v1/post',
                '/v1/post/index',

            ]*/
        ];
        /*$behaviors['access'] = [
            'class' => ApiAccessControl::className(),
            'allowActions' => [
                'v1/post',
                'v1/post/index'
            ]
        ];*/
        return $behaviors;
    }
    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    /*public function checkAccess($action, $model = null, $params = [])
    {
        if(\Yii::$app->user->isGuest){
            return false;
        }else{//var_dump(\Yii::$app->user->can('postindex',$params,true));exit;
            return \Yii::$app->user->can($this->uniqueId,$params,false);
        }
    }*/
}

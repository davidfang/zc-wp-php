<?php
/**
 * Created by David Fang.
 * User: David Fang
 * Date: 2016/1/20
 * Time: 20:29
 */

namespace api\common;

use yii\filters\auth\QueryParamAuth as YiiQueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;
use yii\web\UnauthorizedHttpException;
use zc\rbac\models\search\AuthItemSearch;

class QueryParamAuth extends YiiQueryParamAuth {
    /**
     * @var array List of action that not need to check access.
     * 默认允许访问的路由
     */
    public $allowActions = [];
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'access_token';
    /**
     * 授权验证
     * @param \yii\web\User $user
     * @param \yii\web\Request $request
     * @param \yii\web\Response $response
     * @return null|\yii\web\IdentityInterface
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->get($this->tokenParam);
        $action = \Yii::$app->requestedAction;
        $actionId = $action->getUniqueId();
        //var_dump($actionId);exit;var_dump($actionId);exit;
        if(($accessToken === null || $accessToken === 'null') && in_array('/'.$actionId,$this->allowActions)){
            return $request;
        }
        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
            if ($identity !== null) {
                return $identity;//先不做权限验证，只进行登录验证









                //var_dump(\Yii::$app->requestedAction);
                //var_dump(\Yii::$app);
                //var_dump($request->pathInfo);
                //var_dump($request->queryParams);
                //var_dump(\Yii::$app->requestedRoute);
                //var_dump(\Yii::$app->urlManager->parseRequest($request));exit;


                //var_dump($identity);exit;
                //$auth = \Yii::$app->authManager;
                //$role = $auth->getRolesByUser($identity->id);
                //var_dump($role);
                //var_dump($auth->getPermissionsByUser($identity->id));

                //var_dump($identity);exit;
                //return $identity;



                $params = $request->queryParams;

                $searchModel = new AuthItemSearch(['type' => Item::TYPE_PERMISSION]);
                $searchModel->search([]);
                $auth = \Yii::$app->authManager;
                $permissionsArray = ArrayHelper::getColumn($auth->getPermissions(), 'description');
                //var_dump(\Yii::$app->authManager->getPermissions());
                //var_dump($searchModel->items);
                //var_dump($permissionsArray);

                if(in_array($actionId,$this->allowActions)){
                    return $identity;
                }
                $user = \Yii::$app->user;
                //var_dump($user);
                //var_dump($auth);
                //var_dump($auth->getPermissionsByUser($user->id));
                //exit;
                //exit;
                if ($permissionStr = array_search($actionId, $permissionsArray)) {//echo $permissionStr .'<br>';
                    if ($user->can($permissionStr,$params,false)) {
                        return $identity;
                    }
                }//var_dump($user->can('v1/post/index'));
                /*echo $user->identity->getId() .'<br>';
                foreach ($auth->getPermissionsByUser($user->id) as $p => $ps){
                    echo $p .':====:';
                    echo $user->can($p)?'有权限':'无权限';
                    echo '<br>';
                }exit;*/
                if (array_key_exists('/'.$actionId,$permissionsArray)) { //具体路由的权限验证
                    if ($user->can('/'.$actionId,$params,false)) {
                        return $identity;
                    }//echo 'bbb';
                    //var_dump($user->can('/v1/post/index'));
                    //var_dump($auth->getPermissionsByUser($user->id));

                }
                //echo '/'.$actionId;
                //var_dump(isset($permissionsArray['/'.$actionId]));
                //var_dump(array_key_exists('/'.$actionId,$permissionsArray));
                //var_dump($permissionsArray);
                //exit;
                $controller = $action->controller;//var_dump($controller);exit;
                do {
                    if(in_array(ltrim($controller->getUniqueId() . '/*', '/'),$this->allowActions)){
                        return $identity;
                    }
                    if ($permissionStr = array_search(ltrim($controller->getUniqueId() . '/*', '/'), $permissionsArray)) {//echo $permissionStr .'<br>';
                        if ($user->can($permissionStr,$params,false)) {
                            //exit($permissionStr);
                            return $identity;
                        }
                    }
                    //echo '/'.$controller->getUniqueId(). '/*' .'<br>';
                    //echo ltrim($controller->getUniqueId(). '/*', '/') .'<br>';
                    //var_dump($permissionsArray);
                    $checkUniqueId = $controller->getUniqueId() == '' ? '/*' :'/'.$controller->getUniqueId();
                    if (array_key_exists($checkUniqueId.'/*',$permissionsArray)) { //目录下都可以的权限验证
                        if ($user->can($checkUniqueId.'/*',$params,false)) {
                            return $identity;
                        }
                        //echo '<h1>eeeeeee</h1>';
                    }//echo $checkUniqueId.'/*<br>';
                    if (array_key_exists($checkUniqueId,$permissionsArray)) { //具体路径可以的权限验证
                        if ($user->can($checkUniqueId,$params,false)) {
                            return $identity;
                        }
                    }
                    $controller = $controller->module;
                } while ($controller !== null);//exit;












                throw new UnauthorizedHttpException('对不起，你无权访问',200);
            }else{
                throw new UnauthorizedHttpException('请登录后访问',200);
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}
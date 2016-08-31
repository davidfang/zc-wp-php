<?php
/**
 * Created by David
 * User: David.Fang
 * Date: 2015/4/20
 * Time: 12:38
 */

namespace api\modules\v1\controllers;
use yii\helpers\Html;
use ZhiCaiWX\core\Msg;
use yii\web\Controller;
use ZhiCaiWX\core\Wechat;
use ZhiCaiWX\core\AccessToken;
use ZhiCaiWX\core;
use ZhiCaiWX\core\WechatOAuth;
use ZhiCaiWX\models as ZhiCaiWX_Models;
use ZhiCaiWX\models\Menu;

class WeixinController extends Controller {
    public function init(){
        parent::init();
        /*
         * 服务器配置，详情请参考@link http://mp.weixin.qq.com/wiki/index.php?title=接入指南
         */

        define("WECHAT_URL", 'http://'.$_SERVER["HTTP_HOST"]);
        $cache = \Yii::$app->request->get('cache',false);
        $wechat_current =  ZhiCaiWX_Models\Wechat::getCurrent($cache);

        if($wechat_current){
            define('WECHAT_TOKEN', $wechat_current['Token']);
            define('ENCODING_AES_KEY', $wechat_current['EncodingAESKey']);

            /*
             * 开发者配置
             */
            define("WECHAT_APPID", $wechat_current['AppID']);
            define("WECHAT_APPSECRET", $wechat_current['AppSecret']);
            define("CURL_LOG",(boolean)$wechat_current['curl_log']);
        }
    }
    public function actionIndex()
    {
        echo 'mall-index-index<br><pre>';
        //$weixin = \ZhiCaiWX\models\Wechat::findOne(['use'=>1]);
        $weixin = \ZhiCaiWX\models\Wechat::find()->asArray()->one();
        var_dump($weixin);
        echo '</pre>';
    }
    public function actionView()
    {
        echo 'mall-index-view';
    }
    public $enableCsrfValidation = false;//这里关掉CSRF检查
    /**
     * 测试ZhiCaiWX  微信功能
     * http://www.example.com/m/weixin/weixin.html
     */
    public function actionWeixin()
    {
        //记录所有请求日志  开始 调试使用，正式可以不用
        //get post data, May be due to the different environments
        $postStr = isset($GLOBALS["HTTP_RAW_POST_DATA"])?$GLOBALS["HTTP_RAW_POST_DATA"]:'';
        //extract post data
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msg_type = $postObj->MsgType;
            $_db_request_log = new ZhiCaiWX_Models\RequestLog() ;// \app\modules\weixin\models\RequestLog();
            $_db_request_log->get = $_SERVER["QUERY_STRING"];
            $_db_request_log->post = $postStr;
            //$_db_request_log->created_at = time();
            $_db_request_log->save();
        }
        //记录所有请求日志  结束 调试使用，正式可以不用

        //初始化微信类
        $wechat = new Wechat(WECHAT_TOKEN, TRUE);
        if(!isset($_GET['echostr']))
        {
            //首次使用需要注视掉下面这1行（26行），并打开最后一行（29行）
            echo $wechat->run();
        }else{
            //首次使用需要打开下面这一行（29行），并且注释掉上面1行（26行）。本行用来验证URL
            $wechat->checkSignature();
        }
    }
    /**
     * 网页授权
     * 使用方法：
     * $backurl = '';
     * $url = 'http://' . $_SERVER["HTTP_HOST"] . '/m/weixin/oauth.html?scope=snsapi_usernifo&redirect=' . urlencode($backurl);
    header("location:{$url}");
    exit();
     * scope:snsapi_usernifo/snsapi_base
     */
    public function actionOauth($redirect=null,$scope='snsapi_base',$code=''){
        //第一步，获取CODE
        //$backurl = 'http://' . $_SERVER["HTTP_HOST"] . '/m/online/user.html?code='.$code;
        $redirect_url = '/m/weixin/oauth-back.html?redirect=' . urlencode($redirect);
        core\WechatOAuth::getCode($redirect_url, $state='STATE', $scope);
    }
    /**
     * 网页授权  返回操作
     */
    public function actionOauthBack($redirect=null, $state=1, $scope='snsapi_base',$code=''){
        //第二步，获取access_token网页版
        $accessTokenAndOpenId = core\WechatOAuth::getAccessTokenAndOpenId($code);
        if(isset($accessTokenAndOpenId['errcode'])){//获取access_token失败 记录错误信息
            Msg::saveWeixinErrMsg($accessTokenAndOpenId['errcode'],$accessTokenAndOpenId['errmsg'],__FILE__,__LINE__-2,'$accessTokenAndOpenId = core\WechatOAuth::getAccessTokenAndOpenId('.$code.')');
            Msg::returnErrMsg(500,'授权获取access_token失败');
        }elseif($accessTokenAndOpenId['scope']=='snsapi_base'){//暗授权
            $user_info_parms = 'openid='.$accessTokenAndOpenId['openid'];
        }else{//明授权
            //第三步，获取用户信息
            $userInfo =core\WechatOAuth::getUserInfo($accessTokenAndOpenId['access_token'],$accessTokenAndOpenId['openid']);
            $user_info_parms = http_build_query($userInfo);
        }

        $new_redirect_url = (strpos($redirect,'?')==false)?$redirect.'?'.$user_info_parms:$redirect.'&'.$user_info_parms;
        Msg::saveWeixinErrMsg(666,'网页授权返回地址'.$redirect.'&'.$user_info_parms,__FILE__,__LINE__,'');
        //echo $new_redirect_url;exit;
        header("location:{$new_redirect_url}");
        exit();
    }

    /**
     * 以下都将是一些测试功能
     */
    public function actionWebTest($openid=null){
        if(is_null($openid)){
            echo '无用户微信ID，无法测试';
        }else{
            $basic_user = \app\models\BasicUser::findOne(['fromusername'=>$openid]);
            if(empty($basic_user)){
                echo '错误的微信ID，无法测试';
            }else{
                \Yii::$app->user->login($basic_user, 3600 * 5 );
                $session['openid'] = $openid;
                echo '网页测试登录成功：<br>昵称：'.$basic_user->nickname .'<br>';
                echo Html::a('在线显示',\Yii::$app->urlManager->createUrl('m/online/index')).'<br>';
                echo Html::a('在线显示',\Yii::$app->urlManager->createUrl('m/online/index')).'<br>';

            }
        }
    }
    /**
     * 缓存测试
     */
    public function actionCache(){
        $cache = \Yii::$app->cache;
        echo '<pre>';
        var_dump($cache);

        $key = 'wechat_current';
        $current = $cache->get($key);
        if($current){
            var_dump($current);
        }
        echo '<pre>';
        $flush = $cache->flush();
        $delete = $cache->delete($key);
        var_dump($flush);
        var_dump($delete);
    }
    /**
     * 获取微信access_token
     */
    public function actionAccessToken(){

        $access_token = AccessToken::getAccessToken();
        var_dump($access_token);
    }

    /**
     * 获取用户微信信息
     */
    public function actionGetUserInfo($openId=''){
        $user_info = core\UserManage::getUserInfo($openId);
        echo '<pre>';
        var_dump($user_info);
        echo '</pre>';
    }

    /**
     * 获取菜单
     */
    public function actionGetMenu(){
        $menu = core\Menu::getMenu();
        echo '<pre>';
        var_dump($menu);
        echo '</pre>';
    }

    /**
     * 推送菜单
     */
    public function actionSendMenu(){
        $menuList1 = array(
            array('id'=>'1', 'pid'=>'0', 'name'=>'顶级分类一', 'type'=>'', 'code'=>''),
            array('id'=>'2', 'pid'=>'1', 'name'=>'分类一子类一', 'type'=>'click', 'code'=>'lane_wechat_menu_1_1'),
            array('id'=>'3', 'pid'=>'1', 'name'=>'分类一子类二', 'type'=>'click', 'code'=>'http://www.lanecn.com'),
            array('id'=>'4', 'pid'=>'0', 'name'=>'顶级分类二', 'type'=>'view', 'code'=>'http://www.php.net/'),
            array('id'=>'5', 'pid'=>'0', 'name'=>'顶级分类三', 'type'=>'click', 'code'=>'lane_wechat_menu_3'),

        );

        $menuList2 = array(
            array('id'=>'1', 'pid'=>'0', 'name'=>'链接点击', 'type'=>'', 'code'=>''),
            array('id'=>'2', 'pid'=>'1', 'name'=>'点击click', 'type'=>'click', 'code'=>'lane_wechat_menu_1_1'),
            array('id'=>'3', 'pid'=>'1', 'name'=>'链接view', 'type'=>'view', 'code'=>'http://wp.zhicaikeji.com'),

            array('id'=>'4', 'pid'=>'0', 'name'=>'扫码', 'type'=>'', 'code'=>''),
            array('id'=>'5', 'pid'=>'4', 'name'=>'扫码带提示', 'type'=>'scancode_waitmsg', 'code'=>'rselfmenu_0_0'),
            array('id'=>'6', 'pid'=>'4', 'name'=>'扫码推事件', 'type'=>'scancode_push', 'code'=>'rselfmenu_0_1'),

            array('id'=>'7', 'pid'=>'0', 'name'=>'发图发位置', 'type'=>'click', 'code'=>''),
            array('id'=>'8', 'pid'=>'7', 'name'=>'系统拍照发图', 'type'=>'pic_sysphoto', 'code'=>'rselfmenu_1_0'),
            array('id'=>'9', 'pid'=>'7', 'name'=>'拍照或者相册发图', 'type'=>'pic_photo_or_album', 'code'=>'rselfmenu_1_1'),
            array('id'=>'10', 'pid'=>'7', 'name'=>'微信相册发图', 'type'=>'pic_weixin', 'code'=>'rselfmenu_1_2'),
            array('id'=>'11', 'pid'=>'7', 'name'=>'发送位置', 'type'=>'location_select', 'code'=>'rselfmenu_2_0'),

        );
        //从数据库获取菜单，设置菜单
        $menuList3 = Menu::find()->where(['status'=>'是'])
            ->select(['id', 'pid', 'name', 'type', 'code'])
            ->asArray()->all();
        //$menuList4 = Menu::findOne(['status'=>'是']);

        echo $menuList2==$menuList3 ? '相等':'不相等';

        $menu = core\Menu::setMenu($menuList3);
        echo '<pre>';
        //var_dump($menuList2);
        var_dump($menuList3);
        //var_dump($menuList4);
        var_dump($menu);
        echo '</pre>';
    }

    /**
     * 主动发消息，可以用在用户好友回复消息时候提示
     * @param string $fromusername
     * @param string $msg
     */
    public function actionSendMsg($fromusername='oqvH_jl0VNPQuLlrZwv_yd5Gdfes',$msg='主动发消息给你'){
        $weixin_back_info = core\ResponseInitiative::text($fromusername, $msg);
        if(isset($weixin_back_info['errcode']) and $weixin_back_info['errcode']!=0){
            //throw  '发消息信息错误';
            $line_code = '$weixin_back_info = Core\ResponseInitiative::text('.$fromusername.', '.$msg.');';
            Msg::saveWeixinErrMsg($weixin_back_info['errcode'],$weixin_back_info['errmsg'],__FILE__,__LINE__-4,$line_code);
            //return false;
            Msg::returnErrMsg(10000, '主动发消息失败');
        }
        echo '<pre>';
        var_dump($weixin_back_info);
        echo '</pre>';
    }
}
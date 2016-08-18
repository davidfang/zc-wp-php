<?php
namespace console\controllers;
/**
 * Created by david_fang.
 * User: david_fang
 * Date: 2016/8/17
 * Time: 18:51
 * To change this template use File | Settings | File Templates.
 */
use api\modules\v1\models\MobileVerification;
use Yii;
class QueueController extends \yii\console\Controller
{
    public function actionMobileVerification(){
        $redis = Yii::$app->redis;
        do {
            $mobile = $redis->RPOPLPUSH('queue:MobileVerification', 'queue:MobileVerification:send');
            $check = MobileVerification::checkCode($mobile);
            if ($check) {
                echo $mobile . '===== 5分钟之内发过 /n';
            } else {
                $code = MobileVerification::generator($mobile);
                echo $mobile . '=====' . $code . ' /n';
            }
        }while(1);

    }
    // 这个命令 "yii example/create test" 将调用 "actionCreate('test')"
    public function actionCreate($name) {
        echo $name;
        //return 0;
    }

    // 这个命令 "yii example/index city" 将调用 "actionIndex('city', 'name')"
    // 这个命令 "yii example/index city id" 将调用 "actionIndex('city', 'id')"
    public function actionIndex($category, $order = 'name') {
        echo $category;
        echo $order;
        return 0;
    }

    // 这个命令 "yii example/add test" 将调用 "actionAdd(['test'])"
    // 这个命令 "yii example/add test1,test2" 将调用 "actionAdd(['test1', 'test2'])"
    public function actionAdd(array $name) {
        var_dump($name);
        return 0;
    }
}
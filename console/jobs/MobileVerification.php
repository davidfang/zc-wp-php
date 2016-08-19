<?php
/**
 * Created by david_fang.
 * User: david_fang
 * Date: 2016/8/19
 * Time: 7:29
 * To change this template use File | Settings | File Templates.
 */

namespace console\jobs;

use api\modules\v1\models\MobileVerification as MobileVerificationModel;
use Yii;

class MobileVerification
{
    public function run($job, $data)
    {
        //var_dump($job);
        echo "执行 $data 发送验证码队列 \n";
        //process $data;
        //var_dump($data);
        $mobile = $data;
        if(preg_match("/^1[3|4|5|7|8][0-9]{9}$/",$mobile)){
            //验证通过
            $check = MobileVerificationModel::checkCode($mobile);
            if ($check) {
                echo $mobile . "===== 5分钟之内发过 \n";
            } else {
                $code = MobileVerificationModel::generator($mobile);
                echo $mobile . '=====' . $code . "\n";
            }
        }else{
            //手机号码格式不对
            echo "手机号码格式不对 \n";
        }

    }
}
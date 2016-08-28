<?php
/**
 * Created by david_fang.
 * User: david_fang
 * Date: 2016/8/19
 * Time: 7:29
 * To change this template use File | Settings | File Templates.
 */

namespace console\jobs;
use api\modules\v1\models\Transaction;
use Yii;

class LossProfit
{
    public function run($job, $data)
    {
        //var_dump($job);
        echo "执行止损止盈操作 \n";
        //process $data;
        //var_dump($job);
        //var_dump($data);
        $redis = Yii::$app->redis;
        $transactionInfo = $redis->hget('transaction',$data['id']);
        //var_dump($transactionInfo);
        if(is_null($transactionInfo)){
            echo "单子已经被关闭了 \n";
        }else{
            $transactionInfoArray = json_decode($transactionInfo,true);
            $transactionInfoArray['close_type'] = $data['close_type'];
            $transactionInfoArray['close_price'] = $data['close_price'];
            $transactionInfoArray['close_at'] = $data['close_at'];
            try{
                $cashFlow = Transaction::close($transactionInfoArray);
                echo "订单已经止损止盈关闭，现金流水信息：\n";
                var_dump($cashFlow->toArray());
            }catch (\ErrorException $e){
                //var_dump($e);
                echo '错误了';
                echo $e->getFile() .' (第'.$e->getLine() .'行)';
                echo $e->getMessage();
            }
        }
        echo "================================================\n";
    }
}
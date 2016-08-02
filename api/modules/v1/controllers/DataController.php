<?php
/**
* PostController控制器
* Created by David
* User: David.Fang
* Date: 2016-1-22* Time: 18:57:59*/
namespace api\modules\v1\controllers;
use yii;
//use yii\rest\Controller;
use yii\web\Controller;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
/**
 * 获取数据
 * Class DataController
 * @package api\modules\v1\controllers
 */
class DataController extends Controller
{
    /*public function behaviors()
    {
        return ArrayHelper::merge([
            [
                'class' => Cors::className(),
                'cors' => [
                    //'Origin' => ['http://localhost'],
                    //'Origin' => ['http://localhost:8000'],
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'HEAD', 'OPTIONS'],
                ],
            ],
        ], parent::behaviors());
    }*/
    /**
     * 获取即时数据
     * @return array
     */
    /*public function behaviors()
    {
        return ArrayHelper::merge([
            [
                'class' => Cors::className(),
                'cors' => [
                    //'Origin' => ['http://localhost'],
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'HEAD', 'OPTIONS'],
                ],
            ],
        ], parent::behaviors());
    }*/
    public function behaviors()
  {
      return [
          'corsFilter' => [
              'class' => \yii\filters\Cors::className(),
              'cors' => [
                  // restrict access to
                  'Origin' => ['http://node.dev', 'https://node.dev'],
                 'Access-Control-Request-Method' => ['POST','GET', 'PUT'],
                  // Allow only POST and PUT methods
                  'Access-Control-Request-Headers' => ['X-Wsse'],
                  // Allow only headers 'X-Wsse'
                  'Access-Control-Allow-Credentials' => true,
                  // Allow OPTIONS caching
                  'Access-Control-Max-Age' => 3600,
                  // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                  'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
              ],

          ],
      ];
  }
    public function actionIndex($stock,$time = 'now'){
        Yii::$app->response->format = yii\web\Response::FORMAT_RAW;
        $redis = Yii::$app->redis;
        $key = $stock.':realTime:'.$time;
        //$result1 = $redis->executeCommand('ZREVRANGE',['zset:sliver:realTime:M1',0,30]);
        $result1 = $redis->executeCommand('ZREVRANGE',[$key.':zset',0,30]);
        //array_unshift($result1,'sliver:realTime:M1');
        array_unshift($result1,$key);
        $result = $redis->executeCommand('HMGET',$result1);
        echo 'date	open	high	low	close	volume  adjclose'."\r\n";
        foreach($result as $item){
            echo $item ."\r\n";
        }
       // die('');
        //return $result;
    }
}

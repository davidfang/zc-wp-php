<?php

namespace common\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class MyHelper
{
    /**
     * 打印数组
     * @param $vars
     * @param string $label
     * @param bool $return
     * @return null|string
     */
    public static function dump($vars, $label = '', $return = false)
    {
        if (ini_get('html_errors')) {
            $content = "<pre>\n";
            if ($label != '')
                $content .= "<strong>{$label} :</strong>\n";
            $content .= htmlspecialchars(print_r($vars, true));
            $content .= "\n</pre>\n";
        } else
            $content = $label . " :\n" . print_r($vars, true);
        if ($return)
            return $content;
        echo $content;
        return null;
    }

    /**
     * 获取utf8单个字符字节数
     * @param $str
     * @param int $startpos
     * @return int
     */
    public static function utf8strcount($str, $startpos = 0)
    {
        $c = substr($str, $startpos, 1);
        if (ord($c) < 0x80)
            return 1;
        else {
            if ((ord($c) | 0x1f) == 0xdf)
                return 2;
            else {
                if ((ord($c) | 0xf) == 0xef)
                    return 3;
                else {
                    if ((ord($c) | 0x7) == 0xf7)
                        return 4;
                    else
                        return 0;
                }
            }
        }
    }

    /**
     * 按字符宽度截取utf8字符串，返回 "字符.."
     * @param $in
     * @param $num
     * @param string $endstr
     * @return string
     */
    public static function SubstrUTF8($in, $num, $endstr = "...")
    {
        $pos = 0;
        $strnum = 0;
        $parity = 0;
        $out = "";
        while ($pos < strlen($in)) {
            $count = static::utf8strcount($in, $pos);
            if ($count > 0) {
                if ($count == 1)
                    $parity++;
                else
                    $parity += 2;
                if ($parity / 2 >= $num) {
                    $out .= $endstr;
                    break;
                }
                $c = substr($in, $pos, $count);
                //遇到回车符跳出
                if ($c == "\n" or $c == "\r")
                    $c = " ";
                $out .= $c;
                $pos += $count;
            } else
                break;
        }
        return $out;
    }

    /**
     * 导出csv
     * @param array $recordset
     * @param string $reportname
     * @param array $titlelist
     * @return string
     */
    public static function  csvput(array $recordset, $reportname = "", array $titlelist)
    {
        $keylist = array_keys($titlelist);
        $tmpfield = [];
        foreach ($keylist as $k => $val) {
            $tmpfield[] = iconv("UTF-8", "GB18030", $titlelist[$val]);
        }
        $cpfield = $tmpfield;
        $path = \Yii::getAlias('@backend/web') . '/tmp/' . Date('Ymd') . '/';
        if (!file_exists($path))
            mkdir($path, 0777, true);
        $uploadfile = $path . "report_" . $reportname . "_" . Date('YmdHis') . ".csv";
        $fp = fopen($uploadfile, 'w');
        fputcsv($fp, $cpfield);
        foreach ($recordset as $k => $v) {
            $tmpfield = [];
            for ($ki = 0; $ki < count($keylist); $ki++) {
                $key = $keylist[$ki];
                $tmpfield[] = iconv("UTF-8", "GB18030", $recordset[$k][$key]);
            }
            $cpfield = $tmpfield;
            fputcsv($fp, $cpfield);
        }
        fclose($fp);
        return $uploadfile;
    }

    /**
     * 生成操作按钮
     * @param $url
     * @param string $type  view|update|delete 或者要显示的图标CSS样式
     * @param array $options
     * @return string
     */
    public static function actionbutton($url, $type = 'update', $options = [])
    {
        if ($type == 'view') {
            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ArrayHelper::merge([
                'title'     => Yii::t('yii', 'View'),
                'data-pjax' => '0',
            ], $options));
        }else
        if ($type == 'update') {
            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ArrayHelper::merge([
                'title'     => Yii::t('yii', 'Update'),
                'data-pjax' => '0',
            ], $options));
        }else
        if ($type == 'delete') {
            return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ArrayHelper::merge([
                'title'        => Yii::t('yii', 'Delete'),
                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'data-method'  => 'post',
                'data-pjax'    => '0',
            ], $options));
        }else{
            return Html::a("<span class='glyphicon $type'></span>", $url, ArrayHelper::merge([
                'title'     => Yii::t('yii', 'Update'),
                'data-pjax' => '0',
            ], $options));
        }
    }
    /**
     * 获得角色权限资源（带child）
     * @param $items  角色权限资源数组
     * @param $father 上一级的名字
     * @return array
     */
    public static function itemTree($items,$father=''){
        //$auth = Yii::$app->authManager;
        //$permissions = $auth->getPermissions();
        $return = array();// clone $permissions;//
        $str='';
        foreach ($items as $k => $v) {
            //$str .= ''.$v->name .'-('.$v->type.')-'.'('.$v->description.')<br>';
            $icon = ($v->type ==1)?'icon-user':'icon-eye-open';
            $add_html = MyHelper::actionbutton(['rbac/create', 'father' => $k], 'icon-plus-sign', ['title' => '添加下级']);
            $remove_child_html = MyHelper::actionbutton(['rbac/remove-child','father' =>$father, 'child' => $k], 'icon-minus-sign', ['title' => '从此级删除此项']);
            $del_html = MyHelper::actionbutton('rbac/delete?id=' . $k, 'delete');
            $update_html = MyHelper::actionbutton('update?id=' . $k, 'update');

            $str .= '<li class="dd-item dd2-item" data-id="'.$v->name.'">
													<div class="dd-handle dd2-handle">
														<i class="normal-icon '.$icon.' red bigger-130"></i>

														<i class="drag-icon icon-move bigger-125"></i>
													</div>
													<div class="dd2-content">'.$v->name.'('.$v->description.')
													<div class="pull-right action-buttons">
																	'.$add_html.$remove_child_html.$update_html.$del_html.'
																</div>
													</div>';

            $children = Yii::$app->authManager->getChildren($k);
            if(empty($children)){
                $children = $children ;
                //$str .= '无子';
            }else{
                $children_array = self::itemTree($children,$v->name);
                $children = $children_array['return'];
                //$str .= '='.$children_array['str'];
                $str .= '<ol class="dd-list">';
                $str .= $children_array['str'];
                $str .= '</ol>';
            }
            $str .= '</li>';
            $permission = [
                'type'=>$v->type,
                'name'=>$v->name,//str_replace('/','\/',$v->name),
                '_name'=>str_replace('/','\/',$v->name),//用于树状搜索时便于jquery搜索
                'description'=>$v->description,
                'ruleName'=>$v->ruleName,
                'data'=>$v->data,
                'createdAt'=>$v->createdAt,
                'updatedAt'=>$v->updatedAt,
                'children'=>$children,
            ];
            $return[$k] = $permission;
        }
        return ['return'=>$return,'str'=>$str];
    }
    /**
     * @param $url 上传目标地址
     * @param $model 模型名称model，一般指数据表名
     * @param $field 数据表中需要的字段名称
     * @param string $formname 表单字段名称（接收服务器对方的接收名称）
     * @return mixed
     */
    public static function curlUploadFile($url,$model,$field, $formname = 'file') {
        $uploadFile = \yii\web\UploadedFile::getInstance($model,$field);
        $mimetype = $uploadFile->type;
        $filename = realpath($uploadFile->tempName);
        $upload_file = new \CURLFile($filename,$mimetype);
        $post_data = array($formname => $upload_file);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_HEADER, false); //不输出头部信息
        //curl_setopt($ch,CURLOPT_SAFE_UPLOAD, false);//强制PHP的cURL模块拒绝旧的@语法，仅接受CURLFile式的文件，php5.6已经改为true，我是5.6的，用了@,此处必须为false
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); //成功返回true，输出内容
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        //这里说明一点，CURLOPT_POSTFIELDS 一定要放在前面设置以后，不仅仅是上传，其他curl 操作也这样，保持参数设置有效
        $result =  curl_exec($ch);
        // 调试信息
        // var_dump($result);exit;
        curl_close($ch);
        return json_decode($result,true);
    }

    /**
     * 1.PHP CURL GET EXAMPLE
    You can use the below code to send GET request.
     * @param $url
     * @return mixed
     */
    public static  function curlGet($url)
    {
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
//  curl_setopt($ch,CURLOPT_HEADER, false);

        $output=curl_exec($ch);

        curl_close($ch);
        return $output;
    }

    /**
     * 2.PHP CURL POST EXAMPLE
    PHP CURL POST & GET Examples
    You can use the below code to submit form using PHP CURL.
     * How to use the function:
     *
     * $params = array(
    "name" => "Ravishanker Kusuma",
    "age" => "32",
    "location" => "India"
    );

    echo curlPost("http://hayageek.com/examples/php/curl-examples/post.php",$params);
     *
     * @param $url
     * @param $params
     * @return mixed
     */
    public static function curlPost($url,$params)
    {
        $postData = '';
        //create name value pairs seperated by &
        foreach($params as $k => $v)
        {
            $postData .= $k . '='.$v.'&';
        }
        rtrim($postData, '&');

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, count($postData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $output=curl_exec($ch);

        curl_close($ch);
        return $output;

    }

    /**
     * 3.SEND RANDOM USER-AGENT IN THE REQUESTS
    You can use the below function to get Random User-Agent.
     * Using CURLOPT_USERAGENT, you can set User-Agent string.
     *
     * curl_setopt($ch,CURLOPT_USERAGENT,getRandomUserAgent());
     *
     * @return mixed
     */
    public static function getRandomUserAgent()
    {
        $userAgents=array(
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
            "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
            "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)",
            "Opera/9.20 (Windows NT 6.0; U; en)",
            "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.50",
            "Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.1) Opera 7.02 [en]",
            "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; fr; rv:1.7) Gecko/20040624 Firefox/0.9",
            "Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/48 (like Gecko) Safari/48"
        );
        $random = rand(0,count($userAgents)-1);

        return $userAgents[$random];
    }

    /**
     * 5.HOW TO HANDLE CURL ERRORS
    we can use curl_errno(),curl_error() methods, to get the last errors for the current session.
    curl_error($ch) -> returns error as string
    curl_errno($ch) -> returns error number
    You can use the below code to handle errors.
     *
     *
     * @param $url
     * @return mixed
     */
    public static function curlGetWithErros($url)
    {
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

        $output=curl_exec($ch);

        if($output === false)
        {
            echo "Error Number:".curl_errno($ch)."<br>";
            echo "Error String:".curl_error($ch);
        }
        curl_close($ch);
        return $output;
    }
    /**
     * Checks if the user can perform the operation as specified by the given permission.
     *
     * Note that you must configure "authManager" application component in order to use this method.
     * Otherwise an exception will be thrown.
     *
     * @param string $permissionName the name of the permission (e.g. "edit post") that needs access check.
     * @param array $params name-value pairs that would be passed to the rules associated
     * with the roles and permissions assigned to the user. A param with name 'user' is added to
     * this array, which holds the value of [[id]].
     *
     * @return boolean whether the user can perform the operation as specified by the given permission.
     */
    public static function can($permissionName, $params = []){


    }
}
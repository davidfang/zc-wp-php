<?php
/**
 * Created by david_fang.
 * User: david_fang
 * Date: 2016/8/19
 * Time: 7:29
 * To change this template use File | Settings | File Templates.
 */

namespace console\jobs;


class MyJob
{
    public function run($job, $data)
    {
        //var_dump($job);
        echo '执行MyJob';
        //process $data;
        var_dump($data);
    }
}
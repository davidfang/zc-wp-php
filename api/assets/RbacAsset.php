<?php
/**
 * Created by PhpStorm.
 * User: olebar
 * Date: 2014/10/22
 * Time: 16:32:40
 */

namespace backend\assets;


use yii\web\AssetBundle;

class RbacAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/fuelux/data/fuelux.tree-sampledata.js',
        'js/fuelux/fuelux.tree.min.js'
    ];
    public $depends = [
        'backend\assets\BootstrapjsAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
} 
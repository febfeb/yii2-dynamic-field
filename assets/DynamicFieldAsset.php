<?php

namespace febfeb\dynamicfield\assets;


use yii\web\AssetBundle;

class DynamicFieldAsset extends AssetBundle
{
    public $sourcePath = '@vendor/febfeb/yii2-dynamic-field/web';
    public $css = [
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
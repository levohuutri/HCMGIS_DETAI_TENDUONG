<?php
namespace app\modules\contrib\gxassets;

class GxLimitlessTemplateAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/limitless';

    public $css = [
        'css/animate.css',
        'https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900',
        'css/icons/icomoon/styles.css',
        'css/bootstrap_limitless.min.css',
        'css/bootstrap.min.css',
        'css/components.min.css',
        'css/layout.min.css',
        'css/colors.min.css',
        'css/custom-style.css'
    ];

    public $js = [
        'js/wow.min.js',
        'js/core/app.js'
    ];

    public $depends = [
        '\app\modules\contrib\gxassets\GxBootstrapAsset'
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
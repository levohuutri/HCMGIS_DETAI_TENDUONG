<?php
namespace app\modules\contrib\gxassets;

use yii\bootstrap\BootstrapAsset;


class GxMarzipanoAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/marzipano';

    public $css = [];

    public $js = [
        '//www.marzipano.net/demos/common/es5-shim.js',
        '//www.marzipano.net/demos/common/eventShim.js',
        '//www.marzipano.net/demos/common/requestAnimationFrame.js',
        'marzipano.js'
    ];

    public $depends = [
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
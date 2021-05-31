<?php
namespace app\modules\contrib\gxassets;

class GxVueInfiniteLoadingAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/vue-infinite-loading';

    public $css = [
    ];

    public $js = [
        'vue-infinite-loading.js',
        'axios.min.js'
    ];

    public $depends = [
        'app\modules\contrib\gxassets\GxVueAsset'
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
<?php
namespace app\modules\contrib\gxassets;

class GxLeafletScreenshoterAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/leaflet-plugins/screenshoter';

    public $css = [];

    public $js = [
        'screenshoter.min.js',
        'https://unpkg.com/file-saver@1.3.3/FileSaver.js'
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
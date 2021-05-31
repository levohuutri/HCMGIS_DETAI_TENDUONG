<?php
namespace app\modules\contrib\gxassets;

class GxHtml2CanvasAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/html2canvas';

    public $css = [];

    public $js = [
        'html2canvas.min.js'
    ];

    public $depends = [];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
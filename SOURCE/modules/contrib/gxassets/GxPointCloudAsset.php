<?php
namespace app\modules\contrib\gxassets;

class GxPointCloudAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/point-cloud';

    public $css = [
        'potree/potree.css',
        'jquery-ui/jquery-ui.min.css',
        'perfect-scrollbar/css/perfect-scrollbar.css',
        'openlayers3/ol.css',
        'spectrum/spectrum.css',
        'jstree/themes/mixed/style.css'
    ]; 

    public $js = [
        // 'jquery/jquery-3.1.1.min.js',
        'spectrum/spectrum.js',
        'perfect-scrollbar/js/perfect-scrollbar.jquery.js',
        'jquery-ui/jquery-ui.min.js',
        'three.js/build/three.min.js',
        'other/BinaryHeap.js',
        'tween/tween.min.js',
        'd3/d3.js',
        'proj4/proj4.js',
        'openlayers3/ol.js',
        'i18next/i18next.js',
        'jstree/jstree.js',
        'potree/potree.js',
        'plasio/js/laslaz.js'
    ];

    public $depends = [
        'app\modules\contrib\gxassets\GxJqueryAsset'
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
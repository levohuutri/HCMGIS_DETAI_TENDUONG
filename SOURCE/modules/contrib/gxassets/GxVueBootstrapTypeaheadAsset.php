<?php
namespace app\modules\contrib\gxassets;

class GxVueBootstrapTypeaheadAsset extends \yii\web\AssetBundle {
    public $sourcePath = '@app/modules/contrib/gxassets/assets/vue-bootstrap-typeahead';

    public $css = [
        'typeahead.min.css'
    ];

    public $js = [
        'typeahead.min.js'
    ];

    public $depends = [
        '\app\modules\contrib\gxassets\GxVueAsset',
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
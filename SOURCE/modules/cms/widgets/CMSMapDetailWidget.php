<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 15-Mar-19
 * Time: 8:05 AM
 */

namespace app\modules\cms\widgets;

use yii\base\Widget;

class CMSMapDetailWidget extends Widget
{
    public $lat;
    public $lng;
    public function run()
    {
        $lat = $this->lat ? $this->lat : 16.047079;
        $lng = $this->lng ? $this->lng : 108.206230;
        return $this->render('cmsMapDetailWidget', compact('lat', 'lng'));
    }

}
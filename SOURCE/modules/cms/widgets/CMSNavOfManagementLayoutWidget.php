<?php

namespace app\modules\cms\widgets;

use yii\base\Widget;


class CMSNavOfManagementLayoutWidget extends Widget {
    public function run() {
        return $this->render('cmsNavOfManagementLayoutWidget');
    }
}
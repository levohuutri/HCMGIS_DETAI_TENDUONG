<?php

namespace app\modules\cms\widgets;

use yii\base\Widget;


class CMSSidebarOfManagementLayoutWidget extends Widget {
    public function run() {
        return $this->render('cmsSidebarOfManagementLayoutWidget');
    }
}
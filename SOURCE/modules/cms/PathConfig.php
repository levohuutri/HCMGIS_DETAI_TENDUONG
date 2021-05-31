<?php
 namespace app\modules\cms;

 class PathConfig {
    public static function getAppViewPath($viewname) {
        return '@app/modules/cms/views/' . $viewname;
    }
 }
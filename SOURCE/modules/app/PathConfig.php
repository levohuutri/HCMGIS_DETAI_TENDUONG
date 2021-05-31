<?php
 namespace app\modules\app;

 class PathConfig {
    public static function getAppViewPath($viewname) {
        return '@app/modules/app/views/' . $viewname;
    }
 }
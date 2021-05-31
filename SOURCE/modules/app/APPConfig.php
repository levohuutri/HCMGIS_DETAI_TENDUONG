<?php

namespace app\modules\app;


class APPConfig
{
    public static $ROOT_URL = 'app/';
    public static $URL_KEY = 'hcmgispointcloud2020';

    public static function getUrl($url)
    {
        return \Yii::$app->homeUrl . self::$ROOT_URL . $url;
    }
}
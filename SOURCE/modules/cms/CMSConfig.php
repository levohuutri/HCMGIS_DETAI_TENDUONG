<?php

namespace app\modules\cms;


class CMSConfig
{
    public static $CONFIG = [
        'siteName' => 'HCMGIS',
        'adminSidebar' => [
            'system' => [
                'name' => 'Hệ thống',
                'icon' => 'icon-stack2',
                'url' => 'cms/system'
            ],
            'user' => [
                'name' => 'Người dùng',
                'icon' => 'icon-users',
                'url' => 'cms/user'
            ],
            'pointcloud' => [
                'name' => 'HCMGIS',
                'icon' => 'icon-grid52',
                'url' => 'cms/point-cloud'
            ]
        ],
    ];

    public static $ROOT_URL = 'cms/';
    public static $URL_KEY = 'hcmgispointcloud2020';

    public static function getUrl($url)
    {
        return \Yii::$app->homeUrl . self::$ROOT_URL . $url;
    }
}
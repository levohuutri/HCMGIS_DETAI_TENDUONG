<?php
namespace app\modules\app\services;

class GeoObjectConfig {
    /**
     * ['props' => ['id' => 'gid', name' => '', 'geom' => 'geom']]
     */
    public static $GeoObjectDefault = ['sqlSelect' => 'gid as id, st_asgeojson(geom) as geojson', 'props' => ['id' => 'gid', 'name' => '', 'geom' => 'geom']];
    public static $GeoObjects = [
        'obj_duongcoten' => ['props' => ['id' => 'gid', 'name' => 'tenduong', 'geom' => 'geom']],
        'obj_diemgiatriyte' => ['props' => ['id' => 'gid', 'name' => 'tencsyte', 'geom' => 'geom']],
        'obj_diemgiatrigiaoduc' => ['props' => ['id' => 'gid', 'name' => 'ten_dv', 'geom' => 'geom']],
        'obj_diemgiatritongiao' =>  ['props' => ['id' => 'gid', 'name' => 'ten', 'geom' => 'geom']],
        'obj_diemditichlichsu' => ['props' => ['id' => 'gid', 'name' => 'ten_dt', 'geom' => 'geom']]
    ];

    public static function getGeoPropsByType($geoCode) {
        if (array_key_exists($geoCode, self::$GeoObjects)) {
            return array_merge(self::$GeoObjectDefault, self::$GeoObjects[$geoCode]);
        }
        return null;
    }
}
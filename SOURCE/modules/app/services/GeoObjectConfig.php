<?php
namespace app\modules\app\services;

class GeoObjectConfig {
    /**
     * ['props' => ['id' => 'gid', name' => '', 'geom' => 'geom']]
     */
    public static $GeoObjectDefault = ['sqlSelect' => 'gid as id, st_asgeojson(geom) as geojson', 'props' => ['id' => 'gid', 'name' => '', 'geom' => 'geom']];
    public static $GeoObjects = [
        'obj_duongcoten' => [
            'sqlSelect' => 'gid as id, st_asgeojson(geom) as geojson, tenduong as name', 
            'props' => ['id' => 'gid', 'name' => 'tenduong', 'geom' => 'geom']
        ],
        'obj_diemgiatriyte' => [
            'sqlSelect' => 'gid as id, st_asgeojson(geom) as geojson, tencsyt as name', 
            'props' => ['id' => 'gid', 'name' => 'tencsyte', 'geom' => 'geom']
        ],
        'obj_diemgiatrigiaoduc' => [
            'sqlSelect' => 'gid as id, st_asgeojson(geom) as geojson, tendv as name', 
            'props' => ['id' => 'gid', 'name' => 'ten_dv', 'geom' => 'geom']
        ],
        'obj_diemgiatritongiao' =>  [
            'sqlSelect' => 'gid as id, st_asgeojson(geom) as geojson, ten as name', 
            'props' => ['id' => 'gid', 'name' => 'ten', 'geom' => 'geom']
        ],
        'obj_diemditichlichsu' => [
            'sqlSelect' => 'gid as id, st_asgeojson(geom) as geojson, ten_dt as name', 
            'props' => ['id' => 'gid', 'name' => 'ten_dt', 'geom' => 'geom']
        ]
    ];

    public static function getGeoPropsByType($geoCode) {
        if (array_key_exists($geoCode, self::$GeoObjects)) {
            return array_merge(self::$GeoObjectDefault, self::$GeoObjects[$geoCode]);
        }
        return null;
    }
}
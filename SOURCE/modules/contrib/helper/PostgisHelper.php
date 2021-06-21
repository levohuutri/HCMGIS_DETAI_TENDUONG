<?php

namespace app\modules\contrib\helper;

class PostgisHelper {
    public static function stPoint4326($lat, $lng) {
        return "st_setsrid(st_point($lng, $lat), 4326)";
    }

    public static function stEnvelope4326($neLat, $neLng, $swLat, $swLng) {
        $nePoint = self::stPoint4326($neLat, $neLng);
        $swPoint = self::stPoint4326($swLat, $swLng);

        return "st_setsrid(st_envelope(st_collect($nePoint, $swPoint)), 4326)";
    }
}
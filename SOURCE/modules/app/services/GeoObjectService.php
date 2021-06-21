<?php
namespace app\modules\app\services;

use app\modules\app\models\ModelGeoObjectQueryInBBox;
use app\modules\contrib\helper\PostgisHelper;
use yii\db\Query;

class GeoObjectService {
    public static function getGeoJsonStringByBBox($params) {
        $model = new ModelGeoObjectQueryInBBox();
        $model->load($params, '');
        if ($model->validate()) {
            $geoProps = GeoObjectConfig::getGeoPropsByType($model->geoCode);
            if ($geoProps != null) {
                $envelope = PostgisHelper::stEnvelope4326(
                    $model->northEastLat, $model->northEastLng,
                    $model->southWestLat, $model->southWestLng
                );
                $query = new Query();
                $query->from($model->geoCode)
                    ->select($geoProps['sqlSelect'])
                    ->where("st_contains($envelope, geom)");
                return json_encode([
                    'count' => $query->count(),
                    'data' => $query->all()
                ]);
            }
        }

        return "[]";
    }   
}
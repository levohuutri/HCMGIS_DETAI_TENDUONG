<?php
namespace app\modules\app\services;

use app\modules\app\models\ModelGeoObjectQueryInBBox;
use app\modules\app\models\ModelGeoObjectsSummaryInBBox;
use app\modules\cms\models\GeoType;
use app\modules\contrib\helper\PostgisHelper;
use yii\db\Query;

class GeoObjectService {
    public static function geObjectsByBBox($params, $countOnly=false) {
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
                return [
                    'count' => $query->count(),
                    'data' => $countOnly ? [] : $query->all()
                ];
            }
        }

        return [];
    }   

    public static function getObjectsSummaryByBBox($params) {
        $model = new ModelGeoObjectsSummaryInBBox();
        $model->load($params, '');
        $result = [];
        if ($model->validate()) {
            $envelope = PostgisHelper::stEnvelope4326(
                $model->northEastLat, $model->northEastLng,
                $model->southWestLat, $model->southWestLng
            );
            foreach (GeoObjectConfig::$GeoObjects as $geoCode => $geoProps) {
                $query = new Query();
                $result[$geoCode] = [];
                $result[$geoCode]['count'] = $query->from($geoCode)->select($geoProps['sqlSelect'])->where("st_contains($envelope, geom)")->count();
                $result[$geoCode]['props'] = GeoType::find()->where(['code' => $geoCode])->asArray()->one();
            }
        }

        return $result;
    }
}
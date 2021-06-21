<?php

namespace app\modules\app\models;

class ModelGeoObjectsSummaryInBBox  extends ModelGeoObjectQueryInBBox {
    public $northEastLat, $northEastLng, $southWestLat, $southWestLng;

    public function rules() {
        return [
            [['northEastLat', 'northEastLng', 'southWestLat', 'southWestLng'], 'required'],
            [['northEastLat', 'northEastLng', 'southWestLat', 'southWestLng'], 'number']
        ];
    }
}

<?php

namespace app\modules\app\models;

use app\modules\cms\models\GeoType;
use yii\base\Model;

class ModelGeoObjectQueryInBBox  extends Model {
    public $geoCode;
    public $northEastLat, $northEastLng, $southWestLat, $southWestLng;

    public function rules() {
        return [
            [['geoCode', 'northEastLat', 'northEastLng', 'southWestLat', 'southWestLng'], 'required'],
            [['northEastLat', 'northEastLng', 'southWestLat', 'southWestLng'], 'number'],
            [['geoCode'], 'checkValidGeoCode']
        ];
    }

    public function checkValidGeoCode() {
        if (!$this->hasErrors()) {
            if (GeoType::find()->where(['code' => $this->geoCode])->count() <= 0) {
                $this->addError('geoCode', 'Invalid geoCode');
            }
        }
    }
}
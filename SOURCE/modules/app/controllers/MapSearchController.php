<?php 

namespace app\modules\app\controllers;

use app\modules\app\services\GeoObjectService;
use Yii;
use yii\web\Controller;

class MapSearchController extends Controller {
    public $enableCsrfValidation = false;
    /**-------------VIEWS-----------------*/

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionMap() {
        return $this->renderPartial('map');
    }

    public function actionObjects() {
        return json_encode(GeoObjectService::geObjectsByBBox(Yii::$app->request->get()));
    }

    public function actionObjectsSummary() {
        return json_encode(GeoObjectService::getObjectsSummaryByBBox(Yii::$app->request->get()));
    }

    public function actionKeywords() {
        return $this->renderPartial('keywords');
    }
}
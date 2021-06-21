<?php 

namespace app\modules\app\controllers;

use app\modules\app\APPConfig;
use app\modules\app\models\ModelGeoObjectQueryInBBox;
use app\modules\app\services\GeoObjectService;
use app\modules\cms\models\Map;
use app\modules\cms\services\AuthService;
use app\modules\cms\services\FileService;
use app\modules\cms\services\MapService;
use app\modules\cms\services\PointCloudService;
use app\modules\cms\services\SiteService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
        return GeoObjectService::getGeoJsonStringByBBox(Yii::$app->request->get(), Yii::$app->request->get('countOnly'));
        return $this->renderPartial('objects');
    }

    public function actionKeywords() {
        return $this->renderPartial('keywords');
    }
}
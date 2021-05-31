<?php 

namespace app\modules\app\controllers;

use app\modules\app\APPConfig;
use app\modules\cms\models\Map;
use app\modules\cms\services\AuthService;
use app\modules\cms\services\FileService;
use app\modules\cms\services\MapService;
use app\modules\cms\services\PointCloudService;
use app\modules\cms\services\SiteService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class MapController extends Controller
{
    public $enableCsrfValidation = false;
    /**-------------VIEWS-----------------*/
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionCreate() {
        return $this->render('create');
    }

    public function actionEdit($slug) {
        $map = MapService::GetMapBySlug($slug);
        return $this->render('edit', compact('map'));
    }

    public function actionDetail($slug) {
        return $this->render('detail', compact('slug'));
    }
}
<?php 

namespace app\modules\app\controllers;

use app\modules\cms\services\FileService;
use app\modules\contrib\notifications\services\NotificationService;
use app\modules\cms\services\PointCloudService;
use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actionIndex() {
        return $this->render('index');
    }

    // public function actionTest() {
    //     $userid = Yii::$app->user->id;
    //     NotificationService::UnRead(1);
    //     return true;
    // }
}
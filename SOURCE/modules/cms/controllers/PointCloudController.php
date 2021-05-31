<?php 

namespace app\modules\cms\controllers;

use app\modules\cms\models\PointCloud;
use Yii;
use yii\db\Query;
use yii\web\Controller;

class PointCloudController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = 'admin';


    /**-------------VIEWS-----------------*/
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionStatistic() {
        $points = PointCloud::find()->select('title')->asArray()->all();

        return $this->render('statistic', compact('points'));
    }
}
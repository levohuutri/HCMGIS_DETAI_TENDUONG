<?php 

namespace app\modules\cms\controllers;

use Yii;
use yii\web\Controller;

class SystemController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = 'admin';


    /**-------------VIEWS-----------------*/
    public function actionIndex() {
        return $this->render('index');
    }
}
<?php
/**
 * Description of Module
 *
 * @author admin
 */
namespace app\modules\cms;

use app\modules\cms\services\AuthService;
use yii\web\NotFoundHttpException;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();
    }

    public function beforeAction($action)
    {
        if(!AuthService::IsAdmin()) {
            throw new NotFoundHttpException();
        }
        return true;
    }
}
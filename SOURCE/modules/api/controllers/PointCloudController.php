<?php 

namespace app\modules\api\controllers;

use app\modules\cms\models\ImageFile;
use app\modules\cms\models\ImageObjects;
use app\modules\cms\models\ImageOrdering;
use app\modules\cms\models\VImageObject;
use app\modules\cms\services\ImagesService;
use app\modules\cms\services\PointCloudService;
use app\modules\cms\services\SiteService;
use Yii;
use yii\web\Controller;
use app\modules\contrib\auth\models\AuthUser;

class PointCloudController extends Controller
{
    public $enableCsrfValidation = false;
    
    public function actionGetList() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $data = $request->post();
            $page = isset($data['page']) ? $data['page'] : 1;
            $perpage = isset($data['perpage']) ? $data['perpage'] : 6;
            list($points, $pagination) = PointCloudService::GetPointsViaApi($page, $perpage);
            if($points) {
                $response = [
                    'status' => true,
                    'paginations' => $pagination,
                    'points' => PointCloudService::FormatPointCloudsDataToResponseViaApi($points),                    
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Can not get anything data in this site'           
                ];
            }
            return $this->asJson($response);
        }

        $response = [
            'status' => false,
            'message' => 'Invalid request'
        ];

        return $this->asJson($response);
    }

    public function actionTest123() {
        // $objects = AuthUser::find()->all();
        // foreach($objects as $object) {
        //     $object->slug = SiteService::uniqid();
        //     $object->save();
        // }
        // return $this->render('test');
    }
}
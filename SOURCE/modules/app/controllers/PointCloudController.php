<?php 

namespace app\modules\app\controllers;

use app\modules\app\APPConfig;
use app\modules\cms\models\AuthUser;
use app\modules\cms\models\PointCloud;
use app\modules\cms\services\AuthService;
use app\modules\cms\services\CommentService;
use app\modules\cms\services\FileService;
use app\modules\cms\services\PointCloudService;
use app\modules\cms\services\SiteService;
use app\modules\contrib\notifications\services\NotificationService;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PointCloudController extends Controller
{
    public $enableCsrfValidation = false;
    /**-------------VIEWS-----------------*/
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionTag($key = null) {
        return $this->render('tag', compact('key'));
    }

    public function actionMap() {
        return $this->render('map');
    }

    public function actionUpload() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $create = PointCloudService::Create($request->post());
            if($create) {
                $response = ['status' => true, 'message' => PointCloudService::$RESPONSES['UPLOAD_SUCCESS']];
            } else {
                $response = ['status' => false, 'message' => PointCloudService::$RESPONSES['ACTION_ERROR']];
            }
            return $this->asJson($response);
        }
        return $this->render('upload');
    }

    public function actionReRunPotreeConvert($slug) {
        $point = PointCloud::findOne(['slug' => $slug]);
        if($point->current_step == PointCloudService::$STEP['UPLOADED'] || $point->current_step == PointCloudService::$STEP['RUNNING']) {
            PointCloudService::SendDataToPointCloudServer($point);
        }
    }

    public function actionDetail($slug = null) {
        $point = PointCloudService::GetPointCloudBySlug($slug);
        if($point['type'] == PointCloudService::$TYPE['PUBLIC'] || 
            ($point['type'] == PointCloudService::$TYPE['PRIVATE'] && 
                ($point['created_by'] =  Yii::$app->user->id || AuthService::IsAdmin()))) {

            $onprogress = $point['current_step'] == PointCloudService::$STEP['DONE_WITH_SUCCESS'] ? true : false;
            $point['tags'] = $point['tags'] ? json_decode($point['tags']) : [];
            $point = PointCloudService::GetFullInformation([$point])[0];
            return $this->render('detail', compact('point', 'onprogress'));
        }
        throw new NotFoundHttpException();
    }

    public function actionEdit($slug = null) {
        return $this->render('edit');
    }

    public function actionView($slug = null) {
        $point = PointCloud::find()->where(['slug' => $slug])->one();
        $slug = $point->slug;
        $metadata = json_decode($point->metadata, true);
        $colorType = $metadata['params']['marterial'];
        $edlEnabled = $metadata['params']['edl-enabled'];
        
        return $this->renderPartial('view', compact('slug', 'colorType', 'edlEnabled'));
    }

    /**-------------API-----------------*/
    public function actionGetList($page = 1, $perpage = 9, $keyword = '', $sort = 'most-recent') {
        list($points, $pagination) = PointCloudService::GetPoints($page, $perpage, $keyword, $sort);

        return $this->asJson([
            'status' => true,
            'points' => $points,
            'pagination' => $pagination
        ]);
    }

    public function actionGetPointsByTag($page = 1, $perpage = 9, $tag = '', $sort = 'most-recent') {
        list($points, $pagination) = PointCloudService::GetPointsByTag($page, $perpage, $tag, $sort);
        
        return $this->asJson([
            'status' => true,
            'points' => $points,
            'pagination' => $pagination
        ]);
    }

    public function actionGetPointsMap() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $keyword = $request->post('keyword');
            $keyword = $keyword ? mb_strtolower($keyword) : '';
            $geojson = json_decode($request->post('geojson'), true);
            $points = PointCloudService::GetPointsMap($keyword, $geojson);

            return $this->asJson([
                'status' => true,
                'points' => $points
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionGetComments($page = 1, $perpage = 10, $pointid = null) {
        $pointtype = PointCloudService::$OBJECT_TYPE;
        list($comments, $total) = CommentService::GetComments($pointtype, $pointid, $page, $perpage);

        return $this->asJson([
            'status' => true,
            'comments' => $comments,
            'total' => $total
        ]);
    }

    public function actionChangePublishType() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $result = PointCloudService::ChangePublishType($request->post());
            if($result) {
                Yii::$app->session->setFlash('success', PointCloudService::$RESPONSES['CHANGE_PUBLISH_TYPE_SUCCESS']);
                return $this->asJson(['status' => true]);
            }
            return $this->asJson(['status' => false, 'message' => PointCloudService::$RESPONSES['ACTION_ERROR']]);
        }
        throw new NotFoundHttpException();
    }

    public function actionDelete() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $result = PointCloudService::Delete($request->post());
            if($result) {
                Yii::$app->session->setFlash('success', PointCloudService::$RESPONSES['DELETE_SUCCESS']);
                return $this->asJson(['status' => true]);
            }
            return $this->asJson(['status' => false, 'message' => PointCloudService::$RESPONSES['ACTION_ERROR']]);
        }
        throw new NotFoundHttpException();
    }

    public function actionInteractive() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $result = PointCloudService::Interactive($request->post());

            if($result === true) {
                return $this->asJson([
                    'status' => true
                ]);
            }

            return $this->asJson([
                'status' => false,
                'message' => PointCloudService::$RESPONSES['ACTION_ERROR']
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionCountView() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $pointid = $request->post('pointid');
            $point = PointCloud::findOne($pointid);
            $point->count_view++;
            $point->save();
            return $this->asJson(['status' => true, 'count_view' => $point->count_view]);
        }
        throw new NotFoundHttpException();
    }

    public function actionCountDownload() {

    }

    public function actionDownload($pointid, $type = null) {
        $request = Yii::$app->request;
        $point = PointCloud::findOne($pointid);
        $pointfile = $point->point_file;
        $file = Yii::getAlias('@app') . '\web\uploads\\'.$pointfile;
        if(!file_exists($file)){
            return $this->asJson(['status' => false, 'message' => 'File not found!']);
        } else {
            $point->count_download++;
            $point->save();
            \Yii::$app->response->on(\yii\web\Response::EVENT_AFTER_SEND, function($event) {}, $file);
            return \Yii::$app->response->sendFile($file, $pointfile);
        }
            
        
    }

    public function actionComment() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $content = $request->post('content');
            $pointid = $request->post('pointid');
            $pointtype = PointCloudService::$OBJECT_TYPE;
            $result = CommentService::Comment($pointtype, $pointid, $content);

            if($result) {
                return $this->asJson([
                    'status' => true, 
                    'message' => CommentService::$RESPONSES['COMMENT_SUCCESS']]);
            }
            return $this->asJson([
                'status' => false, 
                'message' => CommentService::$RESPONSES['COMMENT_ERROR']]);
        }
        throw new NotFoundHttpException();
    }

    public function actionUpdateStatus() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $rawData = json_decode($request->getRawBody(), true);
            $point = PointCloud::find()->where(['slug' => $rawData['slug']])->one();
            if($point) {
                if($rawData['status'] == 1 || $rawData['status'] == '1') {
                    $pointurl = APPConfig::getUrl('point-cloud/detail/' . $point->slug);
                    PointCloudService::UpdateStepLog($point, PointCloudService::$STEP['DONE_WITH_SUCCESS']);
                    NotificationService::Send(PointCloudService::$RESPONSES['PROCESS_SUCCESS'], $point->created_by, $pointurl, NotificationService::$ICON['SUCCESS'], NotificationService::$COLOR['SUCCESS']);
                } else {
                    PointCloudService::UpdateStepLog($point, PointCloudService::$STEP['DONE_WITH_ERROR']);
                    NotificationService::Send(PointCloudService::$RESPONSES['PROCESS_ERROR'], $point->created_by, NotificationService::$HOME, NotificationService::$ICON['ERROR'], NotificationService::$COLOR['ERROR']);
                }
                return true;
            }
        }
        throw new NotFoundHttpException();
    }

    public function actionGetEpsgCodes() {
        $codes = PointCloudService::GetEPSGCodes();
        return $this->asJson($codes);
    }

    public function actionGenerateProj4Code() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $epsgSelected = $request->post('epsg');
            $arr = explode("(", $epsgSelected);
            $epsgCode = trim($arr[0]);
            $code = PointCloudService::GenerateProj4Code($epsgCode);
            return $this->asJson(['status' => true, 'code' => $code]);
        }
        throw new NotFoundHttpException();
    }
}
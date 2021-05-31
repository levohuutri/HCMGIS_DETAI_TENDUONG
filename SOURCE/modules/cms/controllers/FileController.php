<?php 

namespace app\modules\cms\controllers;

use app\modules\cms\models\FileRepo;
use app\modules\cms\services\FileService;
use app\modules\cms\services\PointCloudService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class FileController extends Controller
{
    public $enableCsrfValidation = false;
    // public $layout = 'admin';


    /**-------------API-----------------*/
    public function actionUpload() {
        $files = $_FILES['Files'];
        $uploadSuccesses = [];
        $uploadFails = [];
        foreach($files['tmp_name'] as $index => $file_tmp) {
            if(!in_array($files['type'][$index], ['image/jpg', 'image/jpeg', 'image/png']) || $files['size'][$index] > 5242880 || $files['error'][$index]) {
                array_push($uploadFails, $files['name'][$index]);
            } else {
                $image = FileService::Upload($file_tmp, $files['name'][$index]);
                if($image) {
                    FileService::ScaleImageToThumbnail(FileService::$UPLOAD_DIR . $image->path);
                    array_push($uploadSuccesses, [
                        'name' => $image->name,
                        'path' => $image->path
                    ]);
                } else {
                    array_push($uploadFails, $files['name'][$index]);
                }
            }
        }

        $response = [
            'status' => true,
            'successes' => $uploadSuccesses,
            'fails' => $uploadFails
        ];

        return $this->asJson($response);
    }

    public function actionUploadPoint() {
        $files = $_FILES['Points'];
        $uploadSuccesses = [];
        $uploadFails = [];
        foreach($files['tmp_name'] as $index => $file_tmp) {
            if($files['error'][$index] || $files['size'][$index] > 1073741824) {
                array_push($uploadFails, $files['name'][$index]);
            } else {
                $point = FileService::Upload($file_tmp, $files['name'][$index]);
                if($point) {
                    array_push($uploadSuccesses, [
                        'name' => $point->name,
                        'path' => $point->path
                    ]);
                } else {
                    array_push($uploadFails, $files['name'][$index]);
                }
            }
        }

        $response = [
            'status' => true,
            'successes' => $uploadSuccesses,
            'fails' => $uploadFails
        ];

        return $this->asJson($response);
    }

    public function actionDelete() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $file = $request->post('file');
            if($file) {
                FileService::DeleteFile($file);
                return $this->asJson(true);
            }
            return $this->asJson(false);
        }
        throw new NotFoundHttpException();
    }
}
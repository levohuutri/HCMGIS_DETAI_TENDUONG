<?php 

namespace app\modules\app\controllers;

use app\modules\app\APPConfig;
use app\modules\cms\models\AuthUser;
use app\modules\cms\services\AuthService;
use app\modules\cms\services\FileService;
use app\modules\cms\services\MapService;
use app\modules\cms\services\PointCloudService;
use app\modules\cms\services\SiteService;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    public $enableCsrfValidation = false;
    /**-------------VIEWS-----------------*/
    public function actionMyProfile() {
        $profile = AuthService::GetUserProfile();
        return $this->render('my-profile', compact('profile'));
    }

    public function actionMyPointCloud() {
        $user = Yii::$app->user->getIdentity();
        $user = ArrayHelper::toArray($user);
        $user['totalpoints'] = PointCloudService::GetTotalPointsOfUser($user['id']);
        return $this->render('my-point-cloud', compact('user'));
    }

    public function actionMyMap() {
        $user = Yii::$app->user->getIdentity();
        $user = ArrayHelper::toArray($user);
        $user['totalmaps'] = MapService::GetTotalMapsOfUser($user['id']);
        return $this->render('my-map', compact('user'));
    }
    
    public function actionPointCloud($slug) {
        $user = AuthService::GetUserBySlug($slug);
        if($user) {
            if($user['id'] == Yii::$app->user->id) {
                return $this->redirect(APPConfig::getUrl('user/my-point-cloud'));
            }

            $user['following'] = AuthService::CheckFollowingUser($user['id']);
            $user['totalpoints'] = PointCloudService::GetTotalPointsOfUser($user['id']);
            return $this->render('point-cloud', compact('user'));
        }
        throw new NotFoundHttpException();
    }

    /**-------------API-----------------*/
    public function actionChangeAvatar() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $id = $request->post('auth_user_id');
            $avatar = $request->post('avatar');

            $user = AuthUser::findOne($id);
            if($user) {
                FileService::DeleteFile($user->avatar);
                $user->avatar = $avatar;
                $user->save();
                return $this->asJson([
                    'status' => true,
                    'message' => AuthService::$RESPONSES['CHANGE_AVATAR_SUCCESS']
                ]);
            }

            return $this->asJson([
                'status' => false,
                'message' => AuthService::$RESPONSES['ERROR']
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionChangeInformation() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $message = AuthService::ChangeInformation($request->post());

            if($message === true) {
                return $this->asJson([
                    'status' => true,
                    'message' => AuthService::$RESPONSES['CHANGE_INFORMATION_SUCCESS']
                ]);
            }

            return $this->asJson([
                'status' => false,
                'message' => $message
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionChangePassword() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $message = AuthService::ChangePassword($request->post());

            if($message === true) {
                return $this->asJson([
                    'status' => true,
                    'message' => AuthService::$RESPONSES['CHANGE_PASSWORD_SUCCESS']
                ]);
            }

            return $this->asJson([
                'status' => false,
                'message' => $message
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionFollow() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $message = AuthService::FollowUser($request->post());

            if($message === true) {
                return $this->asJson(['status' => true]);
            }

            return $this->asJson([
                'status' => false,
                'message' => AuthService::$RESPONSES['ACTION_ERROR']
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionUnfollow() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $message = AuthService::UnfollowUser($request->post());

            if($message === true) {
                return $this->asJson(['status' => true]);
            }

            return $this->asJson([
                'status' => false,
                'message' => AuthService::$RESPONSES['ACTION_ERROR']
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionGetFollowing() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $following = AuthService::GetFollowing();
            return $this->asJson([
                'status' => true,
                'following' => $following
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionGetFollower() {
        $request = Yii::$app->request;
        if($request->isPost) {
            $follower = AuthService::GetFollower();
            return $this->asJson([
                'status' => true,
                'follower' => $follower
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionGetUserPoints($page = 1, $perpage = 1000, $userid = null, $type = 1) {
        if($userid) {
            $points = PointCloudService::GetUserPoints($userid, $page, $perpage, $type);
            return $this->asJson([
                'status' => true,
                'points' => $points
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionGetUserMaps($page = 1, $perpage = 1000, $userid = null, $type = 1) {
        if($userid) {
            $maps = MapService::GetMapsByUserID($userid, $type);
            return $this->asJson([
                'status' => true,
                'maps' => $maps
            ]);
        }
        throw new NotFoundHttpException();
    }

    public function actionGetInteractedPoints($page = 1, $perpage = 1000, $userid = null, $interact = 'LIKED') {
        if($userid) {
            $points = PointCloudService::GetInteractedPoints($userid, $page, $perpage, $interact);
            return $this->asJson([
                'status' => true,
                'points' => $points
            ]);
        }
        throw new NotFoundHttpException();
    }
}
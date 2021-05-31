<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\modules\cms\services\AuthService;
use app\modules\cms\models\AuthUser;
use app\modules\cms\models\AuthUserInfo;
use app\modules\cms\services\AuthHandler;
use app\modules\cms\services\SiteService;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'oAuthSuccess'],
            ],
        ];
    }

    public function oAuthSuccess($client)
    {
        $authHandler = new AuthHandler($client);
        $authHandler->handle();

        return $this->redirectSuccess();
    }

    public function redirectSuccess()
    {
        return $this->action->redirect(Yii::$app->user->getReturnUrl());
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $referrer = false;
        if (Yii::$app->session->has('referrer')) {
            $referrer = Yii::$app->session->get('referrer');
            Yii::$app->session->remove('referrer');
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', compact('model', 'referrer'));
    }

    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegisterForm();
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->register()) {
                SiteService::SendEmailConfirmEmail($model);
                Yii::$app->session->set('referrer', 'register');
                return $this->redirect(Yii::$app->homeUrl . "site/login");
            }
        }

        return $this->render('register', [
            'model' => $model
        ]);
    }

    public function actionLogout()
    {
        SiteService::WriteLog(Yii::$app->user->id, SiteService::$ACTIVITIES['LOGOUT']);
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionForgotPassword()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $email = $request->post('email');
            $user = AuthUser::findOne(['username' => $email]);
            if (!$user) {
                return $this->asJson(['status' => false, 'message' => AuthService::$RESPONSES['EMAIL_NOT_EXIST']]);
            }
            $user->removePasswordResetToken();
            $user->generatePasswordResetToken();
            if ($user->save()) {
                $mail = SiteService::SendEmailResetPassword($user);
                if (!$mail) {
                    return $this->asJson(['status' => false, 'message' => AuthService::$RESPONSES['ERROR']]);
                }
                return $this->asJson(['status' => true, 'message' => AuthService::$RESPONSES['SEND_EMAIL_RESET_PASSWORD_SUCCESS']]);
            }
        }
        return $this->render('forgot-password');
    }

    public function actionResetPassword($token, $auth)
    {
        $currentTime = time();
        $tokenArr = explode('_', $token);
        $timeSendEmail = intval(end($tokenArr));
        $diffTime = $currentTime - $timeSendEmail;
    
        if($diffTime <= 24 * 60 * 60) {
            $user = AuthUser::find()->where(['and', ['password_reset_token' => $token], ['auth_key' => $auth]])->one();
            if($user) {
                return $this->render('reset-password', compact('auth', 'token'));
            }
        }
        throw new NotFoundHttpException();
    }

    public function actionSetNewPassword(){
        $request = Yii::$app->request;
        if($request->isPost) {
            $auth = $request->post('auth');
            $token = $request->post('token');

            $user = AuthUser::find()->where(['and', ['password_reset_token' => $token], ['auth_key' => $auth]])->one();
            if($user) {
                $validate = AuthService::SetNewPassword($request->post());
                if($validate === true) {
                    $user->password = Yii::$app->getSecurity()->generatePasswordHash($request->post('AuthUser')['password']);
                    $user->save();
                    Yii::$app->session->setFlash('success', AuthService::$RESPONSES['CHANGE_PASSWORD_SUCCESS']);
                    return $this->asJson(['status' => true]);
                }
                return $this->asJson(['status' => false, 'message' => $validate]);
            }
        }

        throw new NotFoundHttpException();
    }

    public function actionConfirmEmail($auth, $token)
    {
        $user = AuthUser::find()->where(['and', ['auth_key' => $auth], ['access_token' => $token]])->one();
        if ($user && !$user->confirmed) {
            $user->confirmed = AuthService::$AUTH_CONFIRM['CONFIRMED'];
            $user->save();
            Yii::$app->session->set('referrer', 'confirm-email');
            return $this->redirect(Yii::$app->homeUrl . "site/login");
        }

        throw new NotFoundHttpException();
    }

    public function actionError()
    {
        return $this->render('error');
    }
}

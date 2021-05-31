<?php
namespace app\modules\cms\services;

use app\modules\cms\models\Auth;
use app\modules\cms\models\AuthUser;
use app\modules\cms\models\AuthUserInfo;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;

/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class AuthHandler
{
    /**
     * @var ClientInterface
     */
    private $client;
    private $data;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getDataFromClient() {
        if ($this->client instanceof yii\authclient\clients\Google) {
            $this->getDataFromGoogleClient();
        }
        if ($this->client instanceof yii\authclient\clients\Facebook) {
            $this->getDataFromFacebookClient();
        }
    }

    public function getDataFromGoogleClient() {
        $attributes = $this->client->getUserAttributes();
        $this->data['email'] = ArrayHelper::getValue($attributes, 'email');
        $this->data['id'] = ArrayHelper::getValue($attributes, 'id');
        $this->data['nickname'] = ArrayHelper::getValue($attributes, 'name');
    }

    public function getDataFromFacebookClient() {
        $attributes = $this->client->getUserAttributes();
        $this->data['email'] = ArrayHelper::getValue($attributes, 'email');
        $this->data['id'] = ArrayHelper::getValue($attributes, 'id');
        $this->data['nickname'] = ArrayHelper::getValue($attributes, 'name');
    }

    public function handle()
    {
        $this->getDataFromClient();
        $email = $this->data['email'];
        $id = $this->data['id'];
        $nickname = $this->data['nickname'];

        $auth = Auth::find()->where([
            'source' => $this->client->getId(),
            'source_id' => $id,
        ])->one();

        if (Yii::$app->user->isGuest) {
            if ($auth) {
                $user = AuthUser::findOne($auth->auth_user_id);
                Yii::$app->user->login($user);
                SiteService::WriteLog($user->id, SiteService::$ACTIVITIES['LOGIN']);
            } else {
                $user = AuthUser::findOne(['username' => $email]);
                if ($email == null) {
                    Yii::$app->session->setFlash('error', "Your " . $this->client->getTitle() . " account is not yet linked to any email.");
                } else if ($email !== null && isset($user)) {
                    Yii::$app->user->login($user);
                    SiteService::WriteLog($user->id, SiteService::$ACTIVITIES['LOGIN']);
                } else {
                    $user = new AuthUser([
                        'username' => $email,
                        'fullname' => $nickname,
                        'auth_role_id' => AuthService::$AUTH_ROLE['USER'],
                        'status' => AuthService::$AUTH_STATUS['ACTIVE'],
                        'delete' => AuthService::$AUTH_DELETE['ALIVE'],
                        'confirmed' => AuthService::$AUTH_CONFIRM['CONFIRMED'],
                        'type' => AuthService::$AUTH_TYPE['PUBLIC'],
                        'slug' => SiteService::uniqid()
                    ]);
                    $user->generatePassword();
                    $user->generateAuthKey();
                    $user->generateAccessToken();
                    
                    $transaction = AuthUser::getDb()->beginTransaction();

                    if ($user->save()) {
                        SiteService::WriteLog($user->id, SiteService::$ACTIVITIES['REGISTER']);

                        $auth = new Auth([
                            'auth_user_id' => $user->id,
                            'source' => $this->client->getId(),
                            'source_id' => (string)$id,
                        ]);

                        $userInfo = new AuthUserInfo([
                            'auth_user_id' => $user->id
                        ]);

                        if ($auth->save() && $userInfo->save()) {
                            $transaction->commit();
                            Yii::$app->user->login($user);
                            SiteService::WriteLog($user->id, SiteService::$ACTIVITIES['LOGIN']);
                        } else {
                            Yii::$app->session->setFlash('error', Yii::t('app', 'Unable to save ' . $this->client->getTitle() . ' account: ' . json_encode($auth->getErrors())));
                        }
                    } else {
                        Yii::$app->session->setFlash('error', Yii::t('app', 'Unable to save ' . $this->client->getTitle() . ' account: ' . json_encode($auth->getErrors())));
                    }
                }
            }
        } else {
            if (!$auth) {
                $auth = new Auth([
                    'auth_user_id' => Yii::$app->user->id,
                    'source' => $this->client->getId(),
                    'source_id' => (string)$id,
                ]);
                if ($auth->save()) {
                    $user = $auth->user;
                    Yii::$app->user->login($user);
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Linked ' . $this->client->getTitle() . ' account.'));
                } else {
                    Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Unable to link ' . $this->client->getTitle() . ' account: ' . json_encode($auth->getErrors())));
                }
            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Unable to link ' . $this->client->getTitle() . ' account. There is another user using it.'));
            }
        }
    }
}
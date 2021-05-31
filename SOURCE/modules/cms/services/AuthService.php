<?php

namespace app\modules\cms\services;

use app\modules\cms\models\AuthFollowing;
use app\modules\cms\models\AuthUser;
use app\modules\cms\models\AuthUserInfo;
use Exception;
use Throwable;
use Yii;
use yii\db\Query;

class AuthService
{
    public static $ACT_LOGIN = 1;
    public static $ACT_LOGOUT = 0;

    public static $AUTH_STATUS = [
        'ACTIVE' => 1,
        'DEACTIVE' => 0
    ];

    public static $AUTH_DELETE = [
        'ALIVE' => 1,
        'DELETED' => 0
    ];

    public static $AUTH_TYPE = [
        'PUBLIC' => 1,
        'PRIVATE' => 0
    ];

    public static $AUTH_CONFIRM = [
        'CONFIRMED' => 1,
        'UNCONFIRMED' => 0
    ];

    public static $AUTH_ROLE = [
        'SUPERUSER' => 1,
        'ADMIN' => 2,
        'USER' => 3
    ];

    public static $RESPONSES = [
        'EMPTY_FIELD' => 'Please fill out the form',
        'INCORRECT_PASSWORD' => 'Incorrect password',
        'PASSWORD_LENGTH' => 'Password length is from 6 to 15 characters',
        'PASSWORD_MATCH' => 'Incorrect confirm password',
        'EMAIL_EXIST' => 'Email is already in use by another account',
        'EMAIL_NOT_EXIST' => 'Email has not been registered for any account',
        'EMAIL_FORMAT' => 'Invalid email',
        'INCORRECT_EMAIL_PASSWORD' => 'Incorrect email or password',
        'NOT_ENOUGH_PERMISSION' => 'You have not enough permission to perform this action',
        'USER_NOT_FOUND' => 'User not found',
        'LOGIN_SUCCESS' => 'Login successfully',
        'REGISTER_SUCCESS' => 'Register acoount successfully',
        'CREATE_SUCCESS' => 'Account created and sent login information to email',
        'UNCONFIRMED' => 'Email has not been confirmed, please check again',
        'DELETE_SUCCESS' => 'Delete user successfully',
        'ACTIVE_SUCCESS' => 'Active user successfully',
        'DEACTIVE_SUCCESS' => 'Deactive user successfully',
        'CHANGE_ROLE_SUCCESS' => 'Change user\'s role successfully',
        'CHANGE_TYPE_SUCCESS' => 'Change user\'s publish type successfully',
        'EMPTY_LIST' => 'Empty list',
        'CHANGE_AVATAR_SUCCESS' => 'Change avatar successfully',
        'CHANGE_INFORMATION_SUCCESS' => 'Change user\'s information successfully',
        'CHANGE_PASSWORD_SUCCESS' => 'Change password successfully',
        'FOLLOW_USER' => 'Follow user',
        'UNFOLLOW_USER' => 'Unfollow user',
        'ERROR' => 'Something went wrong',
        'ACTION_ERROR' => 'Action error',
        'SEND_EMAIL_RESET_PASSWORD_SUCCESS' => 'Instruction for resetting a new password have been sent, please check your email'
    ];

    // AuthService::$RESPONSES['UNCONFIRMED'];

    public static function CreateUser($data) {
        $fullname = $data['AuthUser']['fullname'];
        $username = $data['AuthUser']['username'];
        if(!$fullname || !$username) {
            return self::$RESPONSES['EMPTY_FIELD'];
        } else if(self::CheckUsernameExist($username)) {
            return self::$RESPONSES['EMAIL_EXIST'];
        } else if(!self::CheckEmailFormat($username)) {
            return self::$RESPONSES['EMAIL_FORMAT'];
        } else {
            $model = new AuthUser();
            $model->load($data);
            $password = SiteService::RandomString();
            $model->password = Yii::$app->getSecurity()->generatePasswordHash($password);
            $model->status = self::$AUTH_STATUS['ACTIVE'];
            $model->status = self::$AUTH_STATUS['ACTIVE'];
            $model->delete = self::$AUTH_DELETE['ALIVE'];
            $model->confirmed = self::$AUTH_CONFIRM['UNCONFIRMED'];
            $model->slug = SiteService::uniqid();
            $model->generateAuthKey();
            $model->generateAccessToken();

            if($model->save()) {
                $userInfo = new AuthUserInfo([
                    'auth_user_id' => $model->id
                ]);
                $userInfo->save();
                SiteService::SendEmailInstruction($model, $password);
                SiteService::WriteLog(Yii::$app->user->id, SiteService::$ACTIVITIES['DELETE_USER'], $model->id, $model->className(), $model->fullname);
                return true;
            }
        }
    }

    public static function UpdateUser($data, $id) {
        $fullname = $data['AuthUser']['fullname'];
        $username = $data['AuthUser']['username'];
    }

    public static function IsSuperUser($id = null) {
        $id = $id ? $id : \Yii::$app->user->id;
        $isSuperUser = AuthUser::find()->where(['and', ['id' => $id], ['auth_role_id' => self::$AUTH_ROLE['SUPERUSER']]])->one();
        return $isSuperUser ? true : false;
    }

    public static function IsAdmin($id = null)
    {
        $id = $id ? $id : \Yii::$app->user->id;

        if(self::IsSuperUser($id)) {
            return true;
        }

        $isAdmin = AuthUser::find()->where(['and', ['id' => $id], ['auth_role_id' => self::$AUTH_ROLE['ADMIN']]])->one();
        return $isAdmin ? true : false;
    }

    public static function UserFullName()
    {
        return Yii::$app->user->identity->fullname;
    }

    public static function GetUserByUsername($username) 
    {
        $user = AuthUser::findOne(['username' => $username]);
        return $user;
    }

    public static function GetIdByUsername($username) 
    {
        $user = self::GetUserByUsername($username);
        if($user) {
            return $user->id;
        }
        return false;
    }

    public static function GetUserModel($id = null) 
    {
        $id = $id ? $id : \Yii::$app->user->id;
        $user = AuthUser::findOne($id);
        return $user;
    }

    public static function CheckUsernameExist($username)
    {
        $existUsername = AuthUser::findAll(['username' => $username]);
        if ($existUsername) {
            return true;
        }
        return false;
    }

    public static function CheckEmailFormat($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    public static function CheckPhoneFormat($phone)
    {
        if(strlen($phone) == 10 && preg_match("/^[0-9]{10}$/", $phone)) {
            return true;
        }
        return false;
    }

    public static function CheckPassword($password) 
    {
        $user = self::GetUserModel();
        return Yii::$app->getSecurity()->validatePassword($password, $user->password);
    }

    public static function CheckFollowingUser($userid) 
    {
        $currentUserId = Yii::$app->user->id;
        $following = AuthFollowing::find()->where(['and', ['auth_user_id' => $userid], ['follower_id' => $currentUserId]])->one();
        return $following ? true : false;
    }

    public static function GetUserProfile($id = null) {
        $id = $id ? $id : \Yii::$app->user->id;
        $profile = (new Query())
                        ->select(['info.*', 'auth.username', 'auth.type', 'auth.fullname', 'auth.avatar'])
                        ->from('auth_user as auth')
                        ->leftJoin('auth_user_info as info', 'auth.id = info.auth_user_id')
                        ->where(['auth.id' => $id])
                        ->one();
        return $profile;
    }

    public static function GetUserProfileBySlug($slug = null) {
        $query = (new Query())
                        ->select(['info.*', 'auth.username', 'auth.type', 'auth.fullname', 'auth.avatar', 'auth.auth_role_id', 'auth.confirmed'])
                        ->from('auth_user_info as info')
                        ->leftJoin('auth_user as auth', 'auth.id = info.auth_user_id')
                        ->where(['auth.slug' => $slug]);
        if(!self::IsAdmin()) {
            $query->andWhere(['and', 
                                ['auth.status' => self::$AUTH_STATUS['ACTIVE']], 
                                ['auth.status' => self::$AUTH_DELETE['ALIVE']],
                                ['auth.status' => self::$AUTH_TYPE['PUBLIC']],
                                ['auth.status' => self::$AUTH_CONFIRM['CONFIRMED']]]);
        }
        $profile = $query->one();
        return $profile;
    }

    public static function GetUserBySlug($slug = null) {
        $query = (new Query())
                        ->select(['fullname', 'avatar', 'id'])
                        ->from('auth_user')
                        ->where(['slug' => $slug]);
        if(!self::IsAdmin()) {
            $query->andWhere(['and', 
                            ['status' => self::$AUTH_STATUS['ACTIVE']], 
                            ['status' => self::$AUTH_DELETE['ALIVE']],
                            ['status' => self::$AUTH_TYPE['PUBLIC']],
                            ['status' => self::$AUTH_CONFIRM['CONFIRMED']]]);
        }
        $user = $query->one();
        return $user;
    }

    public static function GetUserAvatar($filename) {
        $path = Yii::$app->homeUrl . ($filename ? 'uploads/' . $filename : 'resources/images/no_avatar.jpg');
        return $path;
    }

    public static function ChangeInformation($data) {
        $userData = $data['AuthUser'];
        $userInfoData = $data['AuthUserInfo'];
        if(!$userData['fullname']) {
            return self::$RESPONSES['EMPTY_FIELD'];
        } else {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $user = self::GetUserModel();
                $user->fullname = $userData['fullname'];

                $userInfo = AuthUserInfo::findOne(['auth_user_id' => $user->id]);
                $userInfo->birthday = $userInfoData['birthday'];
                $userInfo->gender = $userInfoData['gender'];
                $userInfo->address = $userInfoData['address'];
                $userInfo->company = $userInfoData['company'];
                $userInfo->phone = $userInfoData['phone'];

                if($user->save() && $userInfo->save()) {
                    $transaction->commit();
                    return true;
                }
            } catch(Exception $e) {
                $transaction->rollBack();
                return self::$RESPONSES['ERROR'];
            } catch(Throwable $e) {
                $transaction->rollBack();
                return self::$RESPONSES['ERROR'];
            }
        }
        return self::$RESPONSES['ERROR'];
    }

    public static function ChangePassword($data) {
        $userData = $data['AuthUser'];
        if(!$userData['password'] || !$userData['newpassword'] || !$userData['confirmpassword']) {
            return self::$RESPONSES['EMPTY_FIELD'];
        } else if (!self::CheckPassword($userData['password'])){
            return self::$RESPONSES['INCORRECT_PASSWORD'];
        } else if (strlen($userData['newpassword']) < 6 || strlen($userData['newpassword']) > 15) {
            return self::$RESPONSES['PASSWORD_LENGTH'];
        } else if ($userData['newpassword'] != $userData['confirmpassword']) {
            return self::$RESPONSES['PASSWORD_MATCH'];
        } else {
            $user = self::GetUserModel();
            $user->password = Yii::$app->getSecurity()->generatePasswordHash($userData['newpassword']);
            if($user->save()) {
                return true;
            }
        }
        return self::$RESPONSES['ERROR']; 
    }

    public static function SetNewPassword($data) {
        $userData = $data['AuthUser'];
        if(!$userData['password'] || !$userData['cpassword']) {
            return self::$RESPONSES['EMPTY_FIELD'];
        } else if (strlen($userData['password']) < 6 || strlen($userData['password']) > 15) {
            return self::$RESPONSES['PASSWORD_LENGTH'];
        } else if ($userData['password'] != $userData['cpassword']) {
            return self::$RESPONSES['PASSWORD_MATCH'];
        } else {
            return true;
        }
        return self::$RESPONSES['ERROR']; 
    }

    public static function FollowUser($data) {
        $userid = $data['userid'];
        $fullname = $data['fullname'];
        $currentUserId = Yii::$app->user->id;
        
        $following = AuthFollowing::find()->where(['and', ['auth_user_id' => $userid], ['follower_id' => $currentUserId]])->one();
        if(!$following) {
            $following = new AuthFollowing([
                'auth_user_id' => $userid,
                'follower_id' => $currentUserId
            ]);

            if($following->save()) {
                SiteService::WriteLog($currentUserId, SiteService::$ACTIVITIES['FOLLOW_USER'], $userid, Yii::$app->user->identityClass, $fullname);
                return true;
            }
        }

        return false;
    }

    public static function UnfollowUser($data) {
        $userid = $data['userid'];
        $fullname = $data['fullname'];
        $currentUserId = Yii::$app->user->id;

        $following = AuthFollowing::find()->where(['and', ['auth_user_id' => $userid], ['follower_id' => $currentUserId]])->one();
        if($following) {
            $following->delete();
            SiteService::WriteLog($currentUserId, SiteService::$ACTIVITIES['UNFOLLOW_USER'], $userid, Yii::$app->user->identityClass, $fullname);
            return true;
        }

        return false;
    }

    public static function GetFollowing() {
        $currentUserId = Yii::$app->user->id;
        $following = (new Query())
                        ->select(['auth.id', 'auth.fullname', 'auth.slug', 'auth.avatar'])
                        ->from('auth_user as auth')
                        ->leftJoin('auth_following as f', 'f.auth_user_id = auth.id')
                        ->where(['follower_id' => $currentUserId])
                        ->all();
        return $following;
    }

    public static function GetFollower() {
        $currentUserId = Yii::$app->user->id;
        $follower = (new Query())
                        ->select(['auth.id', 'auth.fullname', 'auth.slug', 'auth.avatar'])
                        ->from('auth_user as auth')
                        ->leftJoin('auth_following as f', 'f.follower_id = auth.id')
                        ->where(['auth_user_id' => $currentUserId])
                        ->all();
        return $follower;
    }
}
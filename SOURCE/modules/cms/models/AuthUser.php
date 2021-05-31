<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "auth_user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $created_at
 * @property string $updated_at
 * @property int $status
 * @property string $password_reset_token
 * @property string $access_token
 * @property int $auth_role_id
 * @property int $delete
 * @property int $type
 * @property int $confirmed
 * @property string $avatar
 * @property string $fullname
 * @property string $slug
 * @property int $allow_notify
 */
class AuthUser extends AuthUserBase
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['status', 'auth_role_id', 'delete', 'confirmed', 'type', 'allow_notify'], 'default', 'value' => null],
            [['status', 'auth_role_id', 'delete', 'confirmed', 'type', 'allow_notify'], 'integer'],
            [['username', 'password', 'auth_key', 'password_reset_token', 'access_token', 'avatar', 'fullname', 'slug'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'auth_key' => 'Auth Key',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
            'password_reset_token' => 'Password Reset Token',
            'access_token' => 'Access Token',
            'auth_role_id' => 'Auth Role ID',
            'delete' => 'Delete',
            'confirmed' => 'Confirmed',
            'type' => 'Type',
            'avatar' => 'Avatar',
            'fullname' => 'Fullname',
            'allow_notify' => 'Allow Notify'
        ];
    }

    

    /**
     * @return \yii\db\ActiveQuery
     */

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return  Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }
}

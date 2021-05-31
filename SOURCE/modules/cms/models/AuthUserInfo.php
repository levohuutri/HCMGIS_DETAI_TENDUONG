<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "auth_user_info".
 *
 * @property int $id
 * @property int $auth_user_id
 * @property string $birthday
 * @property int $gender
 * @property string $address
 * @property string $phone
 * @property string $company
 */
class AuthUserInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_user_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_user_id', 'gender'], 'default', 'value' => null],
            [['auth_user_id', 'gender'], 'integer'],
            [['birthday'], 'safe'],
            [['address', 'phone', 'company'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'auth_user_id' => 'Auth User ID',
            'birthday' => 'Birthday',
            'gender' => 'Gender',
            'address' => 'Address',
            'phone' => 'Phone',
            'company' => 'Company'
        ];
    }
}

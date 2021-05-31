<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "auth_following".
 *
 * @property int $id
 * @property int $auth_user_id
 * @property int $follower_id
 */
class AuthFollowing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_following';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_user_id', 'follower_id'], 'default', 'value' => null],
            [['auth_user_id', 'follower_id'], 'integer'],
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
            'follower_id' => 'Follower ID',
        ];
    }
}

<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "auth_auth".
 *
 * @property int $id
 * @property int $auth_user_id
 * @property string $source_id
 * @property string $source
 */
class Auth extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_user_id'], 'default', 'value' => null],
            [['auth_user_id'], 'integer'],
            [['source_id', 'source'], 'string', 'max' => 255],
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
            'source_id' => 'Source ID',
            'source' => 'Source',
        ];
    }
}

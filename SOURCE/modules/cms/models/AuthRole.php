<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "auth_role".
 *
 * @property int $id
 * @property string $role_name
 */
class AuthRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_name' => 'Role Name',
        ];
    }
}

<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "activities_log".
 *
 * @property int $id
 * @property int $auth_user_id
 * @property string $log_time
 * @property string $activity
 * @property string $note
 * @property string $object_type
 * @property int $object_id
 * @property string $object_name
 * @property string $auth_user_name
 */
class ActivitiesLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'activities_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_user_id', 'object_id'], 'default', 'value' => null],
            [['auth_user_id', 'object_id'], 'integer'],
            [['log_time'], 'safe'],
            [['activity', 'note', 'object_type', 'object_name', 'auth_user_name'], 'string', 'max' => 255],
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
            'log_time' => 'Log Time',
            'activity' => 'Activity',
            'note' => 'Note',
            'object_type' => 'Object Type',
            'object_id' => 'Object ID',
            'object_name' => 'Object Name',
            'auth_user_name' => 'Auth User Name',
        ];
    }
}

<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "point_cloud_interactive".
 *
 * @property int $id
 * @property int $auth_user_id
 * @property int $point_cloud_id
 * @property int $is_like
 * @property int $is_follow
 * @property int $rating
 */
class PointCloudInteractive extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'point_cloud_interactive';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_user_id', 'point_cloud_id', 'is_like', 'is_follow', 'rating'], 'default', 'value' => null],
            [['auth_user_id', 'point_cloud_id', 'is_like', 'is_follow', 'rating'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'auth_user_id' => Yii::t('app', 'Auth User ID'),
            'point_cloud_id' => Yii::t('app', '3D Viewer ID'),
            'is_like' => Yii::t('app', 'Is Like'),
            'is_follow' => Yii::t('app', 'Is Follow'),
            'rating' => Yii::t('app', 'Rating'),
        ];
    }
}

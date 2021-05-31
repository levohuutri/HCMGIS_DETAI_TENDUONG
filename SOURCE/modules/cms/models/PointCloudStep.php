<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "point_cloud_step".
 *
 * @property int $id
 * @property int $point_cloud_id
 * @property int $step
 * @property string $log
 * @property string $time
 * @property string $note
 */
class PointCloudStep extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'point_cloud_step';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['point_cloud_id', 'step'], 'default', 'value' => null],
            [['point_cloud_id', 'step'], 'integer'],
            [['time'], 'safe'],
            [['note'], 'string'],
            [['log'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'point_cloud_id' => Yii::t('app', '3D Viewer ID'),
            'step' => Yii::t('app', 'Step'),
            'log' => Yii::t('app', 'Log'),
            'time' => Yii::t('app', 'Time'),
            'note' => Yii::t('app', 'Note'),
        ];
    }
}

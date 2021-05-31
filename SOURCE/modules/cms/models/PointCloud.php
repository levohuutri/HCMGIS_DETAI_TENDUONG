<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "point_cloud".
 *
 * @property int $id
 * @property string $title
 * @property string $lat
 * @property string $lng
 * @property string $thumbnail
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $status
 * @property int $delete
 * @property string $description
 * @property string $slug
 * @property int $type
 * @property int $current_step
 * @property string $tags
 * @property int $count_view
 * @property int $count_download
 * @property string $metadata
 * @property string $point_file
 * @property string $collectors
 * @property string $reference
 * @property string $count_points
 */
class PointCloud extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'point_cloud';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'status', 'delete', 'type', 'current_step', 'count_view', 'count_download', 'count_points'], 'default', 'value' => null],
            [['created_by', 'status', 'delete', 'type', 'current_step', 'count_view', 'count_download', 'count_points'], 'integer'],
            [['description', 'tags', 'metadata', 'reference'], 'string'],
            [['title', 'lat', 'lng', 'thumbnail', 'slug', 'point_file', 'collectors'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'lat' => Yii::t('app', 'Lat'),
            'lng' => Yii::t('app', 'Lng'),
            'thumbnail' => Yii::t('app', 'Thumbnail'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
            'delete' => Yii::t('app', 'Delete'),
            'description' => Yii::t('app', 'Description'),
            'slug' => Yii::t('app', 'Slug'),
            'type' => Yii::t('app', 'Type'),
            'current_step' => Yii::t('app', 'Current Step'),
            'tags' => Yii::t('app', 'Tags'),
            'count_view' => Yii::t('app', 'Count View'),
            'count_download' => Yii::t('app', 'Count Download'),
            'metadata' => Yii::t('app', 'Metadata'),
            'point_file' => Yii::t('app', 'Point File'),
            'collectors' => Yii::t('app', 'Collectors'),
            'reference' => Yii::t('app', 'Reference'),
            'count_points' => Yii::t('app', 'Count Points'),
        ];
    }
}

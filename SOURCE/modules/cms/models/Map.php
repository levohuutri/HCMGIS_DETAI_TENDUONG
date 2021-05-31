<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "map".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $base_layers
 * @property string $overlay_layers
 * @property string $pointcloud_ids
 * @property string $created_at
 * @property int $created_by
 * @property int $status
 * @property int $delete
 * @property int $publish_type
 * @property int $count_view
 * @property string $thumbnail
 * @property string $slug
 * @property string $hash
 */
class Map extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'map';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description', 'base_layers', 'overlay_layers', 'pointcloud_ids'], 'string'],
            [['created_at'], 'safe'],
            [['created_by', 'status', 'delete', 'publish_type', 'count_view'], 'default', 'value' => null],
            [['created_by', 'status', 'delete', 'publish_type', 'count_view'], 'integer'],
            [['title', 'thumbnail', 'slug', 'hash'], 'string', 'max' => 255],
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
            'description' => Yii::t('app', 'Description'),
            'base_layers' => Yii::t('app', 'Base Layers'),
            'overlay_layers' => Yii::t('app', 'Overlay Layers'),
            'pointcloud_ids' => Yii::t('app', 'Pointcloud Ids'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'status' => Yii::t('app', 'Status'),
            'delete' => Yii::t('app', 'Delete'),
            'publish_type' => Yii::t('app', 'Publish Type'),
            'count_view' => Yii::t('app', 'Count View'),
            'thumbnail' => Yii::t('app', 'Thumbnail'),
            'slug' => Yii::t('app', 'Slug'),
            'hash' => Yii::t('app', 'Hash'),
        ];
    }
}

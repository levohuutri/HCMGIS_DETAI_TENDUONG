<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "file_ref".
 *
 * @property int $id
 * @property int $image_id
 * @property int $object_id
 * @property string $object_type
 * @property int $status
 * @property string $created_at
 * @property int $version
 */
class FileRef extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file_ref';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image_id', 'object_id', 'status', 'version'], 'default', 'value' => null],
            [['image_id', 'object_id', 'status', 'version'], 'integer'],
            [['created_at'], 'safe'],
            [['object_type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image_id' => 'Image ID',
            'object_id' => 'Object ID',
            'object_type' => 'Object Type',
            'status' => 'Status',
            'created_at' => 'Created At',
            'version' => 'Version',
        ];
    }
}

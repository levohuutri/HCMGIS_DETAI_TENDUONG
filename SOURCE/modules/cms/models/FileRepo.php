<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "file_repo".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $path
 * @property int $created_by
 * @property string $created_at
 * @property string $updated_at
 * @property string $type
 * @property int $delete
 * @property double $width
 * @property double $height
 */
class FileRepo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file_repo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'delete'], 'default', 'value' => null],
            [['created_by', 'delete'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['width', 'height'], 'number'],
            [['name', 'slug', 'path', 'type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'path' => 'Path',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'type' => 'Type',
            'delete' => 'Delete',
            'width' => 'Width',
            'height' => 'Height',
        ];
    }
}

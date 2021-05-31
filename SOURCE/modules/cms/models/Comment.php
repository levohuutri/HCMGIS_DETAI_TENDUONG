<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $object_id
 * @property string $content
 * @property int $status
 * @property int $delete
 * @property string $created_at
 * @property int $created_by
 * @property string $object_type
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['object_id', 'status', 'delete', 'created_by'], 'default', 'value' => null],
            [['object_id', 'status', 'delete', 'created_by'], 'integer'],
            [['content'], 'string'],
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
            'id' => Yii::t('app', 'ID'),
            'object_id' => Yii::t('app', 'Object ID'),
            'content' => Yii::t('app', 'Content'),
            'status' => Yii::t('app', 'Status'),
            'delete' => Yii::t('app', 'Delete'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'object_type' => Yii::t('app', 'Object Type'),
        ];
    }
}

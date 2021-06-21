<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "geo_type".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $status
 * @property string $geom_type
 */
class GeoType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'geo_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => null],
            [['status'], 'integer'],
            [['name', 'code', 'geom_type'], 'string', 'max' => 255],
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
            'code' => 'Code',
            'status' => 'Status',
            'geom_type' => 'Geom Type',
        ];
    }
}

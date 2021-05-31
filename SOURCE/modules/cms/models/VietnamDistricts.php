<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "vietnam_districts".
 *
 * @property int $fid
 * @property string $geom
 * @property string $objectid
 * @property string $ma
 * @property string $ten
 * @property string $cap
 * @property string $matp
 * @property string $tinh_thanh
 * @property string $name
 * @property string $province
 * @property string $lat
 * @property string $lng
 */
class VietnamDistricts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vietnam_districts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fid'], 'required'],
            [['fid'], 'default', 'value' => null],
            [['fid'], 'integer'],
            [['geom'], 'string'],
            [['objectid'], 'number'],
            [['ma', 'ten', 'cap', 'matp', 'tinh_thanh', 'name', 'province'], 'string', 'max' => 254],
            [['lat', 'lng'], 'string', 'max' => 255],
            [['fid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fid' => Yii::t('app', 'Fid'),
            'geom' => Yii::t('app', 'Geom'),
            'objectid' => Yii::t('app', 'Objectid'),
            'ma' => Yii::t('app', 'Ma'),
            'ten' => Yii::t('app', 'Ten'),
            'cap' => Yii::t('app', 'Cap'),
            'matp' => Yii::t('app', 'Matp'),
            'tinh_thanh' => Yii::t('app', 'Tinh Thanh'),
            'name' => Yii::t('app', 'Name'),
            'province' => Yii::t('app', 'Province'),
            'lat' => Yii::t('app', 'Lat'),
            'lng' => Yii::t('app', 'Lng'),
        ];
    }
}

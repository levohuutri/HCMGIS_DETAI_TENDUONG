<?php
/**
 * Description of Module
 *
 * @author admin
 */
namespace app\modules\api;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();
    }

    public static function allowedDomains() {
        return [
            // '*',                        // star allows all domains
            'http://localhost',
            'http://opendata.hcmgis.vn',
            'https://opendata.hcmgis.vn',
            'http://new-opendata.hcmgis.vn'
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return array_merge(parent::behaviors(), [
    
            // For cross-domain AJAX request
            'corsFilter'  => [
                'class' => \yii\filters\Cors::className(),
                'cors'  => [
                    // restrict access to domains:
                    'Origin'                           => static::allowedDomains(),
                    'Access-Control-Request-Method'    => ['POST'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age'           => 3600,                 // Cache (seconds)
                ],
            ],
    
        ]);
    }
}
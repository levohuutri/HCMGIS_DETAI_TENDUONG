<?php
/**
 * Description of Module
 *
 * @author admin
 */
namespace app\modules\contrib;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        $this->modules = [
            'notifications' => [
                'class' => 'app\modules\contrib\notifications\Module'
            ],
            'helper' => [
                'class' => 'app\modules\contrib\helper\Module'
            ],
            'gxassets' => [
                'class' => 'app\modules\contrib\gxassets\Module'
            ],
            'proxy' => [
                'class' => 'app\modules\contrib\proxy\Module'
            ]
        ];
    }
}
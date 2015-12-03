<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @package yii2-icons
 * @version 1.4.0
 */

namespace dbsparkle-team\unispot\assets;

use yii\web\AssetBundle;

class FlagIconAsset extends AssetBundle
{
    public $sourcePath = '@vendor/components/flag-icon-css';
    public $depends = array(
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset'
    );

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->css = YII_DEBUG ? ['css/flag-icon.css'] : ['css/flag-icon.min.css'];
        parent::init();
    }
}
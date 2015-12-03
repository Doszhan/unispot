<?php

namespace dbsparkle-team\unispot\assets;

use yii\web\AssetBundle;

class GeocompleteAsset extends AssetBundle
{
    public $sourcePath = '@geolocation/assets/js';
    public $css = [
    ];
    public $js = [
        'jquery.geocomplete.min.js',
    ];
    public $depends = [
        //'yii\web\JqueryAsset',
        'yii\web\YiiAsset'
    ];


}

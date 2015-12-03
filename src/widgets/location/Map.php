<?php

namespace dbsparkle-team\unispot\widgets\location;

use dbsparkle-team\unispot\exceptions\Exception;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class Map extends Widget
{
    public $apiKey;

    public $options;

    public $pluginOptions;

    public function init()
    {
        if (empty($this->apiKey)) {
            throw new Exception('ApiKey must be set.');
        }
    }

    public function run()
    {
        $this->register();

        echo Html::tag('div', '', $this->options);
    }

    protected function register()
    {
        $mapOptions = Json::encode($this->pluginOptions);
        $this->view->registerJsFile('//maps.googleapis.com/maps/api/js?key=' . $this->apiKey);
        $this->view->registerJs(<<< JS
function initialize() {
    var mapOptions = $mapOptions;
var map = new google.maps.Map(document.getElementById('map-canvas'),
    mapOptions);
}
google.maps.event.addDomListener(window, 'load', initialize);
JS
        );
    }
}

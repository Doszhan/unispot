<?php

namespace dbsparkle-team\unispot\widgets\location;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use dbsparkle-team\unispot\assets\GeocompleteAsset;
use yii\helpers\Json;

/**
 * Widget represents wrapper for [geocomplete]( https://github.com/ubilabs/geocomplete )
 * Renders input, label (if needed) and map if property $enableMap is true (set by default)
 * Usage:
 * It has 2 use cases:
 * - Populate field's data by from available list (full list is shown on http://ubilabs.github.io/geocomplete/examples/form.html ) when $enableForm is true.
 *  By default widget looking in data-geo attribute instead name
 * - Just using geocomplete without populating field data
 * Example:
 * ```
 * Search::widget([ *
 *       'enableMap' => false,
 *       'containerId' => $id,
 *       'options' => [
 *           'id' => 'geocomplete',
 *           'class' => 'form-control',
 *           'placeholder' => Yii::$app->translate->t('Enter a postal code or a place name')
 *       ],
 *       'label' => Yii::$app->translate->t('Address search')
 *   ]);
 * ```
 * @property array $pluginOptions plugin options for jquery.geocomplete.js
 * @property string $label label text for input
 * @property array $labelOptions html options for label
 * @property array $options html options for input
 * @property string $address preset value for input
 * @property bool $displayPopup flag allowing to show popup while clicking on mapMarker
 * @property string $mapContainerClass class for map container
 * @property string $containerId needed to bind form container where locates fields to populate
 * @property bool $enableForm flag meant that data should be populated to fields
 * @property bool $enableMap show map
 *
 * @author Dmitry Fedorov <klka1@live.ru>
 * @since 1.0 2015-06-07
 */
class Search extends InputWidget
{
    public $pluginOptions = [];
    public $label = null;
    public $labelOptions = [];
    public $options = [];
    public $address = '';
    public $displayPopup = false;
    /**
     * Latitude
     * @var float
     */
    public $lat;
    /**
     * Longitude
     * @var float
     */
    public $lng;
    /**
     * @var bool
     */
    public $displayMarker = true;
    /**
     * @var int
     */
    public $mapZoom = 14;
    /**
     * @var string
     */
    public $popupData;
    /**
     * In pixels
     * @var int
     */
    public $mapWidth = 400;
    /**
     * In pixels
     * @var int
     */
    public $mapHeight = 400;
    /**
     * Html options for map container
     * @var array
     */
    public $mapOptions = [];
    /**
     * Css class for map canvas
     * @var string
     */
    public $mapContainerClass = 'map_canvas';
    /**
     * Html id of the div container;
     * @var string
     */
    public $containerId;
    /**
     * @var bool
     */
    public $enableMap = true;
    /**
     * @var bool
     */
    public $enableForm = true;
    /**
     * Google Api key
     *
     * @var null|string
     */
    public $apiKey;
    /**
     * @var null|array
     */
    public $data;
    /**
     * Not null only when we need use widget like part of other form
     * @var null|ActiveForm
     */
    public $form;
    /**
     * Fields, which need to exclude from rendering
     * @var array
     */
    public $excludeFields = [];


    public function init()
    {
        $this->registerClientScript();

        if ($this->enableForm && !$this->containerId) {
            throw new \Exception('geocomplete should contain container id');
        }
        $defaultOptions = [
            'id' => 'geocomplete',
            'placeholder' => 'Enter a postal code or a place name'
        ];
        $this->options = array_merge($defaultOptions, $this->options);
        if(isset($this->options['id'])) {
            $this->id = $this->options['id'];
        }
        /* if($model->country)
          $this->pluginOptions = ['country' => $model->country]; */
        //$jsScript .= '$geocomplete.geocomplete("autocomplete").setComponentRestrictions({country : "'.$model->country.'"});' ;
        //https://github.com/ubilabs/geocomplete
        //https://developers.google.com/maps/documentation/javascript/3.exp/reference#MarkerOptions
        if ($this->enableMap) {
            $defaultMapOptions = [
                'class' => $this->mapContainerClass,
                'style' => 'width: ' . $this->mapWidth . 'px; height: ' . $this->mapHeight . 'px;'
            ];
            $this->mapOptions = array_merge($defaultMapOptions, $this->mapOptions);

            $this->pluginOptions = array_merge([
                'map' => '.' . $this->mapContainerClass,
                'mapOptions' => [
                    'zoom' => $this->mapZoom,
                ],
                'markerOptions' => [
                    'draggable' => true,
                    //'visible' => $this->displayMarker,
                    'disabled' => !$this->displayMarker,
                ]
            ], $this->pluginOptions);
        }
        if ($this->enableForm) {
            $this->pluginOptions = array_merge([
                'details' => '#' . $this->containerId,
                'detailsAttribute' => 'data-geo',
                'types' => [
                    'geocode',
                    'establishment'
                ],
            ], $this->pluginOptions);
        }


        if ($this->lat && $this->lng) {
            $this->pluginOptions['location'] = [$this->lat, $this->lng];
        } elseif (!$this->address && $this->model && $this->model->latitude && $this->model->longitude) {
            $this->pluginOptions['location'] = [$this->model->latitude, $this->model->longitude];
        }
        $jsScript = 'var $geocomplete = $("#' . $this->id . '"),
             options = ' . Json::encode($this->pluginOptions) . ';
             $geocomplete.geocomplete(options);

             ' . /* var $form = $("#google-map-form-'.$this->containerId.'");
          var first = true;
          $geocomplete.bind("geocode:result", function(event, result){
          var $input;
          console.log(result);
          for(property in result){
          $input = $form.find("input[data-geo=\'" + property + "\']");
          console.log($input.length);
          if($input && (!first || $input.val() == ""))
          $input.val(result[property]);
          }
          first = false;
          });
          var coder = new google.maps.Geocoder();
          coder.geocode({address:"-33.460, -70.649"}, function(results, status){
          if (status == google.maps.GeocoderStatus.OK) {
          console.log(results);
          }
          });
          '. */'

             if($geocomplete.val())
                $geocomplete.geocomplete("find", $geocomplete.val());
             else if(options.location){
                $geocomplete.geocomplete("find", options.location[0] + ", " + options.location[1]);
             }
            ';


        if ($this->displayPopup) {
            $jsScript .= '
                var map = $geocomplete.geocomplete("map");
                var marker = $geocomplete.geocomplete("marker");

                var contentString = "' . $this->popupData . '";

                google.maps.event.addListener(marker, "click", function(e) {
                    if(!contentString){
                        if($geocomplete.val())
                            contentString = $geocomplete.val();
                        else{
                            var geocoder = new google.maps.Geocoder();
                            var latlng = new google.maps.LatLng(marker.internalPosition.k, marker.internalPosition.D);
                            geocoder.geocode({"latLng": latlng}, function(results, status) {
                                if (status == google.maps.GeocoderStatus.OK){
                                    contentString = results[0].formatted_address;
                                }
                            });
                        }
                        contentString = contentString + "</br>" + "Longitude: " + marker.internalPosition.D + "</br>" + "Latitude: " + marker.internalPosition.k + "</br>";
                    }
                    contentString = "<div>" + contentString + "</div>";

                    var infowindow = new google.maps.InfoWindow({content: contentString});
                    infowindow.open(map,marker);
                });
                ';
        }

        /* $jsScript .= 'var $selectCountry = $("#'.Html::getInputId($model, 'country').'");
          $selectCountry.change(function(){
          var $option = $selectCountry.find("option[value=" + $(this).val() + "]");
          console.log($option);
          if($option){
          $geocomplete.geocomplete("autocomplete").setComponentRestrictions({country : $option.attr("value")});
          $geocomplete.val("");
          }
          });
          '; */

        \Yii::$app->getView()->registerJs($jsScript);
    }

    public function run()
    {     
        $content = '';
        if ($this->label !== null) {
            $content .= Html::label($this->label, null, $this->labelOptions);
        }
        $content .= Html::textInput('map', $this->address, $this->options);
        if ($this->enableMap) {
            $content .= Html::tag('div', '', $this->mapOptions);
        }


        return $content;
    }

    public function registerClientScript()
    {
        $this->getView()->registerJsFile('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places' . ($this->apiKey ? "&key=" . $this->apiKey : '') . '&language=' . \Yii::$app->language);
        GeocompleteAsset::register($this->getView());
    }
}

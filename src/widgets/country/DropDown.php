<?php

namespace dbsparkleTeam\unispot\widgets\country;

use yii\widgets\InputWidget;
use dbsparkleTeam\unispot\assets\FlagIconAsset;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;
use dbsparkleTeam\unispot\loaders\Country;
use Yii;
use dbsparkleTeam\unispot\components\FlagIcon;

/**
 * Dropdown widget for country picker
 * example
 * ```
 * <?=
 * DropDown::widget([
 *  'name' => 'country',
 *  'value' => Yii::$app->language,
 *  'countries' => ['RU','FI','GB','FR','US','JP','CN'] // If needs to show customized list of countries
 * ]);
 * ?>
 * ```
 *
 * @property array $countries List of shown countries, empty array if need to show all countries
 */
class DropDown extends InputWidget
{
    public $pluginOptions = [];

    public $pluginEvents = [];

    public $countries = [];

    public function run()
    {
        FlagIconAsset::register($this->getView());

        $locales = [];
        $languages = [];
        if(!empty($this->countries)) {
            array_walk($this->countries, function(&$data) {
                $data = strtolower($data);
            });
        }
        $countryRepository = new Country;
        $data = $countryRepository->findAll();
        foreach ($data as $code => $lang) {
            if(empty($this->countries) || in_array(strtolower($code),$this->countries)) {
                $locales[$code] = FlagIcon::flag($code);
                $languages[$code] = $lang->name['english']['common'].' ('.reset($lang->name['native'])['common'].')';
            }
        }

        $format = '
        function format(state) {
            var locales = ' . Json::encode($locales) . ';
            if (!state.id) { return state.text; }

            return locales[state.id] + " " + state.text;
        }';
        $escape = new JsExpression('function(m) { return m; }');
        $this->getView()->registerJs($format, View::POS_HEAD);
        $this->options = array_merge([
            'placeholder' => Yii::$app->translate->t('Choose country')
        ], $this->options);
        $pluginOptions = array_merge([
            'templateResult' => new JsExpression('format'),
            'templateSelection' => new JsExpression('format'),
            'escapeMarkup' => $escape,
        #'minimumResultsForSearch' => -1
        ], $this->pluginOptions);

        $pluginEvents = array_merge([],$this->pluginEvents);

        return Select2::widget([
            'model' => $this->model,
            'attribute' => $this->attribute,
            'name' => $this->name,
            'value' => $this->value,
            'options' => $this->options,
            'data' => $languages,
            'pluginOptions' => $pluginOptions,
            'pluginEvents' => $pluginEvents,
        ]);
    }
}

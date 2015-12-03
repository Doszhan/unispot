<?php

namespace dbsparkle-team\unispot\widgets\currency;

use yii\widgets\InputWidget;
use dbsparkle-team\unispot\assets\FlagIconAsset;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;
use dbsparkle-team\unispot\loaders\Country;
use Yii;
use dbsparkle-team\unispot\components\FlagIcon;

/**
 * Dropdown widget for currency picker
 * example
 * ```
 * <?=
 * DropDown::widget([
 *  'name' => 'currencyId',
 *  'value' => Yii::$app->language,
 *  'currencies' => SystemCurrency::find()->all()
 * ]);
 * ?>
 * ```
 *
 */

class CurrencyDropDown extends InputWidget
{
    /**
     * @var array Plugin options for Select2 widget
     */
    public $pluginOptions = [];
    
    /**
     * @var array Plugin events for Select2 widget
     */
    public $pluginEvents = [];
    
    /**
     * @var array Array of shown currencies, which will be shown in dropdown list
     */
    public $currencies = [];

    /**
     * @inheritdoc
     */
    public function run()
    {

        FlagIconAsset::register($this->getView());

        $locales = [];
        $ids = [];
        $countryRepository = new Country;
        $data = $countryRepository->findAll();

        foreach ($this->currencies as $value) {
            $key = $value->id;
            if (!empty($value->country_flag)){
                $locales[$key] = FlagIcon::flag($value->country_flag);
            } else {
                // iterate data (countries list) to find country code with defined ($value->code) currency
                foreach ($data as $code => $country_value) {
                    if (strcasecmp($country_value->currency['code'], $value->code)==0){
                        $locales[$key] = FlagIcon::flag($code);
                        break;
                    }
                }
            }
            $ids[$key] = $value->code;
        }

        $currencyFormat = 'function currencyFormat(state) {
            var locales = ' . Json::encode($locales) . ';
            if (!state.id) { return state.text; }
            return locales[state.id] + " " + state.text;
        }';
        $escape = new JsExpression('function(m) { return m; }');
        $this->getView()->registerJs($currencyFormat, View::POS_HEAD);
        $this->options = array_merge([
            'placeholder' => Yii::$app->translate->t('select currency')
        ], $this->options);
        $pluginOptions = array_merge([
            'templateResult' => new JsExpression('currencyFormat'),
            'templateSelection' => new JsExpression('currencyFormat'),
            'escapeMarkup' => $escape,
        ], $this->pluginOptions);

        $pluginEvents = array_merge([],$this->pluginEvents);

        return Select2::widget([
            'model' => $this->model,
            'attribute' => $this->attribute,
            'name' => $this->name,
            'value' => $this->value,
            'options' => $this->options,
            'data' => $ids,
            'pluginOptions' => $pluginOptions,
            'pluginEvents' => $pluginEvents,
        ]);
    }
}

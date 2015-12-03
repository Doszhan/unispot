<?php

namespace dbsparkle-team\unispot\widgets\phone;

use dbsparkle-team\unispot\exceptions\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use dbsparkle-team\unispot\widgets\country\DropDown;
use yii\widgets\InputWidget;
use Yii;
use yii\widgets\MaskedInput;
use yii\bootstrap\ButtonDropdown;
use dbsparkle-team\unispot\loaders\Country;
use dbsparkle-team\unispot\assets\FlagIconAsset;
use dbsparkle-team\unispot\components\FlagIcon;

/*
    Examples:

     <?= PhoneInput::widget([
        'model' => $model,
        'attribute' => 'phone',
        'countries' => ['ru', 'gb'] // you can use following array to determine which countries you want to show
        'buttonOptions' => []
    ]); ?>

    //  advanced example, build activField and use ii for usage in tabular form
    <?= PhoneInput::widget([
    'label' => false,
    'model' => $model,
    'form' => $form,
    'attribute' => "phone",
    'id' => 'myId',
    'hint' => Yii::$app->translate->t('some hint', 'rereca-app'),
    'options' => [
        'placeholder' => Yii::$app->translate->t('phone number', 'rereca-app') . '...',
        'name' => $model->formName() . "[{$index}][phone]",
        'label' => false
    ],
    'buttonOptions' => [
        'options' => [
            'class' => 'btn-default',
            'id' => 'buttonId',
        ]
    ],
    'tabularFormRowIndex' => $index,
    ]) ?>

 */
class PhoneInput extends InputWidget
{
    /*
     * @var kartik\widgets\ActiveForm
     */
    public $form;

    /**
     * @var array selectors
     */
    public $s = [];

    /**
     * @var bool  Show label or not
     */
    public $label = true;

    /**
     * @var set this propery if you use this wiget with tabular form
     *
     * <input type="text" name="PhoneOrg[0][phone]">, there index is 0.
     */
    public $tabularFormRowIndex;

    /**
     * List of shown countries, empty array if need to show all countries
     * @var array
     */
    public $countries = [];

    /**
     * List of options to configure button view
     * @var array
     */
    public $buttonOptions = [];

    /**
     * Dropdown configuration for render in buttonDropdown
     * @var array
     */
    public $dropdown = [];

    /**
     * Adds hint under buttonDropdown, on null creates default
     * @var string|null
     */
    public $hint = '';

    /**
     * PhoneInput mask for maskedInput
     * @var string|null
     */
    public $mask = null;

    /**
     * Variable to store countries full list.
     * @var Country|null
     */
    private $_countryRepository;

    /**
     * @var array
     */
    private $_js = [];

    public function init()
    {
        FlagIconAsset::register($this->getView());

        if (isset($this->tabularFormRowIndex)) {
            if (!is_numeric($this->tabularFormRowIndex)) {
                throw new InvalidConfigException('`tabularFormRowIndex` must be numeric.');
            }

            $this->id .= "-{$this->tabularFormRowIndex}";
        }

        $this->s = ArrayHelper::merge([
            'class' => [
                'widgetContainer' => 'phone-input-container',
                'col' => 'col-sm-12',
                'inputMask' => 'input-mask',
            ]
        ], $this->s);

        $this->_countryRepository = new Country;
        $callingCodes = $this->_countryRepository->getCallingCodes();

        if (!isset($this->buttonOptions['label'])) {
            $this->buttonOptions['label'] = Yii::$app->translate->t('choose country', 'unispot');
        }
        if (!isset($this->buttonOptions['containerOptions'])) {
            $this->buttonOptions['containerOptions'] = [
                'class' => 'input-group-btn'
            ];
        }
        if (!isset($this->buttonOptions['options'])) {
            $this->buttonOptions['options'] = [
                'class' => 'btn-default'
            ];
        }

        // Set Japan as defaule value
        $this->model->country_code = ($this->model->country_code) ?: 'JP';

        $inputId = Html::getInputId($this->model, 'country_code');

        if ($this->mask === null) {
            $this->mask = '9{3,15}';    //  digital, length from 3 to 15
            if ($this->model->{$this->attribute} && $this->model->country_code) {
                $countryCode = strtoupper((string)($this->model->country_code));
                $this->mask = '+' . preg_replace('/(.)/i', "\\\\$1", $callingCodes[$countryCode]) . $this->mask;
                if (
                    $this->model->{$this->attribute} &&
                    strpos($this->model->{$this->attribute}, '+' . $callingCodes[$countryCode]) === 0
                ) {
                    $this->model->{$this->attribute}  = substr(
                        $this->model->{$this->attribute},
                        mb_strlen('+' . $callingCodes[$countryCode])
                    );
                }
            }
        }

        if ($this->hint === null) {
            $this->hint = Yii::$app->translate->t('To choose your country enter your address below', 'unispot');
        }

        $this->options = array_merge([
            'id' => $this->id,
            'class' => 'form-control',
            'placeholder' => Yii::$app->translate->t('phone number...', 'unispot')
        ], $this->options);
    }

    public function run()
    {
        $data = $this->_countryRepository->findAll();

        $items = [];
        foreach ($data as $code => $lang) {
            if ($this->countries === [] || in_array(strtolower($code), $this->countries)) {
                $locales[$code] = FlagIcon::flag($code);
                $items[$code] = [
                    'label' => Html::a(
                        $locales[$code] . ' ' . $lang->name['english']['common'] . ' (' . reset($lang->name['native'])['common'] . ')',
                        '#',
                        [
                            'class' => 'changePhoneMask',
                            'data-region' => $code
                        ]
                    )
                ];
            }
        }

        $this->dropdown = array_merge($this->dropdown, [
            'items' => $items,
            'encodeLabels' => false,
        ]);

        $callingCodes = $this->_countryRepository->getCallingCodes();
        $codes = Json::encode($callingCodes);

        $this->_js[] = <<<JS

var dialCodes = $codes;
$(".changePhoneMask").on("click", function () {

    var region = $(this).data("region"),
        data = dialCodes[region],
        list =  $(this).closest("ul"),
        container = list.parent(),
        widgetContainer = $(this).closest(".{$this->s['class']['widgetContainer']}"),
        span = container ? container.prev() : false,
        input = $(".{$this->s['class']['inputMask']}", widgetContainer);

    if(undefined !== data && !data.startsWith("+")) {
        data = "+" + data.replace(/(.)/g,"\\\\$1");
    } else {
        data = "";
    }

    list.siblings("button").html($(this).html() + " <span class='caret'></span>");
    list.dropdown("toggle");

    input
        .prop("disabled", false)
        .inputmask({"mask": data + "9{3,15}"})
        .val("")
        .focus();

    return false;
});

$(".changePhoneMask").click(function(event) {
    var widgetContainer = $(this).closest(".{$this->s["class"]["widgetContainer"]}");
    $("#" + widgetContainer.data("country-code-id")).val( $(this).data("region") );
});

$(".{$this->s["class"]["widgetContainer"]}").each(function(index) {
  var widgetContainer = $(this),
    countryCode = $("#" + widgetContainer.data("country-code-id")).val();

  if (countryCode) {
    var object = $('.changePhoneMask[data-region=' + countryCode + ']', widgetContainer);
      object.closest('ul').siblings('button').html(object.html() + " <span class='caret'></span>");
  }
});
JS;

        $this->getView()->registerJs(join("\r", $this->_js));
        return $this->render('index', [
            'widget' => $this,
            's' => $this->s,
        ]);
    }

    /**
     * Returns input id by attribute.
     *
     * @author Andreev <andreev1024@gmail.com>
     * @param $attribute
     *
     * @return string
     */
    public function getInputId($attribute)
    {
        if (isset($this->tabularFormRowIndex)) {
            $attribute = "[{$this->tabularFormRowIndex}]{$attribute}";
        }
        return Html::getInputId($this->model, $attribute);
    }
}

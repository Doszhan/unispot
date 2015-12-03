<?php

namespace dbsparkle-team\unispot\widgets\phone;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use dbsparkle-team\unispot\assets\FlagIconAsset;
use dbsparkle-team\unispot\components\FlagIcon;
use dbsparkle-team\unispot\components\GeoIP;
use yii\helpers\Html;
use yii\base\Widget;
use libphonenumber\NumberParseException;
use dbsparkle-team\unispot\loaders\Country;
use common\models\PhoneOrg;
/**
 * PhoneNumber is a widget wrapper to show formatted phone with tel: link and country flag
 * usage
 * ```
 * PhoneNumber::widget([
 *     'model' => $model,
 *     'format' => PhoneNumber::INTERNATIONAL // if format is not set, then phone of your country will be formatted in national instead international format
 * ]);
 * ```
 */
class PhoneNumber extends Widget
{
    const E164 = 0;
    const INTERNATIONAL = 1;
    const NATIONAL = 2;
    const RFC3966 = 3;


    private static $_callingCodes;
    private $_showLocal;
    public $model = null;
    public $format = null;


    public function init()
    {
        parent::init();
        if (!$this->model) {
            throw new \Exception('model not found');
        }
        $view = $this->getView();
        if ($this->format === null) {
            $this->_showLocal = true;
        }

        FlagIconAsset::register($view);
    }

    public function run()
    {
        try {
            $countryCode = $this->model->country_code;
            $country = strtolower($countryCode);
            if ($this->_showLocal) {
                $showLocal = strtolower(GeoIP::getCountryCode()) == $country;
                $type = $showLocal ? self::NATIONAL : self::INTERNATIONAL;
            } else {
                $type = $this->format;
            }
            if ($this->model->type === PhoneOrg::TYPE_FAX) {
                $iconNumber = Html::tag('i', '', [
                    'class' => 'fa fa-fax text-success margin-left-10'
                ]);
            } else {
                $iconNumber = Html::tag('i', '', [
                    'class' => 'fa fa-phone-square text-success margin-left-10'
                ]);
            }
            $phone = self::format($this->model->phone, $country, $type);
            return Html::a(FlagIcon::flag($countryCode) . ' ' . $phone . $iconNumber, null, [
                'href' => 'tel:' . $phone
            ]);
        } catch (\Exception $exc) {

        }

        return '';
    }

    public static function format($value, $countryCode, $phoneNumberFormat = null)
    {
        try {
            if ($phoneNumberFormat === null && !in_array($phoneNumberFormat, [
                self::E164,
                self::INTERNATIONAL,
                self::NATIONAL,
                self::RFC3966
            ])) {
                $phoneNumberFormat = self::INTERNATIONAL;
            }
            return self::PhoneUtil()->format(static::PhoneUtil()->parse('+' . trim($value, '+'), $countryCode), $phoneNumberFormat);
        } catch (NumberParseException $ex) {
            return '';
        }
    }

    /**
     *
     * @return PhoneNumberUtil
     */
    public static function PhoneUtil()
    {
        return PhoneNumberUtil::getInstance();
    }

    /**
     * Normalizing phone number
     *
     * @version 1.0.12 2015-09-25
     * @author Dmitry Fedorov <klka1@live.ru>
     * @param string $phone
     * @param string $countryCode
     * @return string
     */
    public static function normalizePhone($phone, $countryCode)
    {
        if (empty(self::$_callingCodes)) {
            $countryRepository = new Country;
            self::$_callingCodes = $countryRepository->getCallingCodes();
        }
        if(!$countryCode) {
            $countryCode = 'JP';
        }
        $newPhone = static::PhoneUtil()->normalizeDigitsOnly($phone);
        $callingCode = self::$_callingCodes[strtoupper($countryCode)];

        if (!$newPhone || $newPhone == $callingCode) {
            return '';
        }
        if (strpos($newPhone, $callingCode) !== 0) {
            $newPhone = $callingCode . $newPhone;
        }
        return '+' . $newPhone;
    }
}

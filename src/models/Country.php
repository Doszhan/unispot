<?php

namespace dbsparkleTeam\unispot\models;

use yii\base\Model;

class Country extends Model
{
    public $name;

    public $tld;

    public $countryCode;

    public $currency;

    private $callingCode;

    public $capital;

    public $continent;

    public $languages;

    public function getCallingCode()
    {
        return $this->callingCode;
    }

    public function setCallingCode($callingCode)
    {
        $this->callingCode = is_array($callingCode) ? reset($callingCode) : $callingCode;
    }
    public function getCountryNumericCode() {
        return intval($this->countryCode['isoNumeric']);
    }
    
}

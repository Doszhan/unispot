<?php

namespace dbsparkle-team\unispot\components;

use yii\helpers\Html;
use yii\base\Object;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use yii\log\Logger;


class GeoIP extends Object
{
    const DEFAULT_USER_IP = '8.8.8.8';

    /**
     * Method checks country code by native php function or search in db file if native function doesn't exist
     * @author Dmitry Fedorov <klka1@live.ru>
     * @version 1.0.2 on 2015-08-05
     * @return string|null
     */
    public static function getCountryCode()
    {
        $country = null;
        if (function_exists('geoip_country_code_by_name')) {

            // trick for local using (@author Constantine <constantinchuprik@gmail.com>)
            $userIp = \Yii::$app->getRequest()->getUserIP();
            if ($userIp == '127.0.0.1' || $userIp == '::1') {
                $userIp = self::DEFAULT_USER_IP;
            }

            $country = geoip_country_code_by_name($userIp);
        } else {
            $geoReader = new Reader(\Yii::getAlias('@geolocation/data/GeoLite2-Country.mmdb'));
            try {
                $country = $geoReader->country(\Yii::$app->getRequest()->getUserIP());
                $country = $country->country->isoCode;
            } catch (AddressNotFoundException $e) {
                \Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_WARNING);
            }
        }
        return $country;
    }
}

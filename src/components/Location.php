<?php

namespace dbsparkleTeam\unispot\components;

use Yii;
use yii\base\Object;
use Ivory\HttpAdapter\CurlHttpAdapter;
use Geocoder\Provider\GoogleMaps;
use Geocoder\Provider\GeoIP2;
use Geocoder\Adapter\GeoIP2Adapter;
use GeoIp2\Database\Reader;
use yii\helpers\Url;
use dbsparkleTeam\unispot\formatters\AddressFormatter;
use Geocoder\Model\AddressCollection;

class Location extends Object
{
    const MAP_TYPE_GOOGLE = 0;
    const MAP_TYPE_YANDEX = 1;
    const MAP_TYPE_OPENSTREET = 2;

    /**
     * Gets for input string containing address or ip
     * @param string $streetAddressOrIp
     * @return AddressCollection
     */
    public static function getLocationData($streetAddressOrIp)
    {
        $curl = new CurlHttpAdapter();

        if (filter_var($streetAddressOrIp, FILTER_VALIDATE_IP)) {
            $reader = new Reader(Yii::getAlias('@geolocation/data/GeoLite2-Country.mmdb'));
            $adapter = new GeoIP2Adapter($reader);
            $geocoder = new GeoIP2($adapter);
        } else {
            $geocoder = new GoogleMaps($curl);
        }
        return $geocoder->geocode($streetAddressOrIp);
    }

    /**
     * usage e.g.
     * ```
     * use \dbsparkleTeam\unispot\formatters\AddressFormatter;
     * use \dbsparkleTeam\unispot\components\Location;
     *
     * Location::getAddress(60.023554,30.2232882,[
     *     'type' => AddressFormatter::TYPE_FULL,
     *     'format' => AddressFormatter::FORMAT_NORMAL
     * ])
     * ```
     * @param float $lat
     * @param float $lng
     * @param array $options
     * @return string
     */
    public static function getAddress($lat, $lng, $options = [])
    {
        $curl = new CurlHttpAdapter();
        $geocoder = new GoogleMaps($curl);
        $addressData = $geocoder->reverse($lat, $lng);
        if(!isset($options['format']) || !in_array($options['format'],[
            AddressFormatter::FORMAT_NORMAL,
            AddressFormatter::FORMAT_REVERSED
            ])) {
            $options['format'] = AddressFormatter::FORMAT_NORMAL;
        }
        if(!isset($options['type']) || !in_array($options['type'],[
            AddressFormatter::TYPE_FULL,
            AddressFormatter::TYPE_SHORT
            ])) {
            $options['type'] = AddressFormatter::TYPE_FULL;
        }
        return AddressFormatter::format($addressData->first(), $options);
    }

    /**
     *
     * @param float $lat
     * @param float $lng
     * @param array $options
     * @return string
     */
    public static function getMapUrl($lat, $lng, $options = [])
    {
        if (!isset($options['type'])) {
            $options['type'] = self::MAP_TYPE_GOOGLE;
        }
        switch ($options['type']) {
            case self::MAP_TYPE_GOOGLE:
                $url = Url::to('http://google.com/maps?q=' . $lat . ',' . $lng);
                break;
            case self::MAP_TYPE_YANDEX:
                $url = Url::to('http://maps.yandex.ru/?text=' . $lat . ',' . $lng);
                break;
            case self::MAP_TYPE_OPENSTREET:
                $url = Url::to('http://openstreetmap.org/#map=15/' . $lat . '/' . $lng);
                break;
        }

        return $url;
    }
}

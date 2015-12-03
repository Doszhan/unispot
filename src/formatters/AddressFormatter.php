<?php

namespace dbsparkle-team\unispot\formatters;
use Geocoder\Model\Address;

class AddressFormatter extends Formatter
{
    const TYPE_FULL = 0;
    const TYPE_SHORT = 1;

    const FORMAT_NORMAL = 0;
    const FORMAT_REVERSED = 1;

    public static function format(Address $address, $options) {
        $data = array_filter([
            'country' => $address->getCountry()->getName(),
            'city' => $address->getLocality(),
            'district' => $address->getSubLocality(),
            'postalCode' => $address->getPostalCode(),
            'streetName' => $address->getStreetName(),
            'streetNumber' => $address->getStreetNumber(),
        ]);
        if($options['format'] === self::FORMAT_REVERSED) {
            $data = array_reverse($data);
        }
        if($options['type'] === self::TYPE_SHORT) {
            unset($data['country']);
        }
        return implode(', ',$data);
    }
}

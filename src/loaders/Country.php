<?php

namespace dbsparkleTeam\unispot\loaders;

class Country extends Loader
{
    const FILE_ALIAS = '@geolocation/data/general/countries.json';

    public function loadFile()
    {
        return json_decode(file_get_contents(\Yii::getAlias(self::FILE_ALIAS)), true);
    }

    protected function createModel($data)
    {
        return new \dbsparkleTeam\unispot\models\Country($data);
    }

    public function getCallingCodes($filter = true)
    {
        $result = [];
        foreach($this->fileData as $code => $data) {
            $value = reset($data['callingCode']);
            if ($filter && $value === false) {
                continue;
            }
            $result[$code] = $value;
        }

        return $result;
    }

    public function getCountryList()
    {
        $result = [];
        foreach($this->fileData as $code => $data) {
            $countryName = $data['name']['english']['common'];
            $result[$code] = is_array($countryName) ? reset($countryName) : $countryName;
        }

        return $result;
    }

    public function getContinentList()
    {
        $result = [];
        foreach($this->fileData as $country) {
            if(!isset($result[$country['continent']['code']])) {
                $result[$country['continent']['code']] = $country['continent']['name'];
            }
        }

        return $result;
    }
}

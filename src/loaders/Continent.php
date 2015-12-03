<?php

namespace dbsparkle-team\unispot\loaders;

class Continent extends Loader
{
    const FILE_ALIAS = '@geolocation/data/general/continents.json';

    public function loadFile()
    {
        return json_decode(file_get_contents(\Yii::getAlias(self::FILE_ALIAS)), true);
    }

    protected function createModel($data)
    {
        return new \dbsparkle-team\unispot\models\Continent($data);
    }
}

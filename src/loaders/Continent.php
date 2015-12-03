<?php

namespace dbsparkleTeam\unispot\loaders;

class Continent extends Loader
{
    const FILE_ALIAS = '@geolocation/data/general/continents.json';

    public function loadFile()
    {
        return json_decode(file_get_contents(\Yii::getAlias(self::FILE_ALIAS)), true);
    }

    protected function createModel($data)
    {
        return new \dbsparkleTeam\unispot\models\Continent($data);
    }
}

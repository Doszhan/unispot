<?php

namespace dbsparkleTeam\unispot\components;

use yii\helpers\Html;
use yii\base\Object;

class FlagIcon extends Object
{
    public static function flag($countryCode)
    {
        return Html::tag('span', '', [
            'class' => 'flag-icon flag-icon-' . strtolower($countryCode)
        ]);
    }
}

UNISPOT is a collection of location/area related utilities for Yii2

1. **Components**
    1. GeoIP component - methods:
        * static - `getCountryCode()` - gets code by user's ip
    1. FlagIcon component - methods:
        * static - `flag($countryCode)` - returns html tag rendering flag-icon depending on 2-letter isoCode
    1. Location component - methods:
        * static - `getLocationData($streetAddressOrIp)` - returns AddressCollection object
        * static - `getAddress($lat, $lng, $options = [])` - returns string with address using AddressFormatter() - for detail explanation see example below
        * static - `getMapUrl($lat, $lng, $options = [])` - returns url for Google/Yandex/OpenStreet map depending on coordinates
2. **Widgets**
    * PhoneInput widget
    * CountryDropdown widget
    * Map
    * Searchbox (geocomplete)
3. **Formatters**
    * Phone formatter
    * Address formatter

----

REQUIREMENTS
------------
This  requires the following modules/libs:   

 * Yii2 >= 2.0.5   
 * Jquery >= 2.1.4   
 * Twitter Bootstrap ~ 3.x


CONFIGURATION
-------------
 * In common way for all needed things you should use widgets or formatters

 * Country dropdown.
  Dropdown widget for country picker

  Example:

```
  <?=
  DropDown::widget([
   'name' => 'country',
   'value' => Yii::$app->language,
   'countries' => ['RU','FI','GB','FR','US','JP','CN'] // If needs to show customized list of countries
  ]);
  ?>
```
* PhoneNumber format widget

Example:

```
PhoneNumber::widget([
    'model' => $model,
    'format' => PhoneNumber::INTERNATIONAL // if format is not set, then phone of your country will be formatted in national instead international format
]);
```

* PhoneInput widget

Example:

```
//PhoneInput extends from inputWidget so you can replace model + attribute with 'name' property if you dont have model
<?= PhoneInput::widget([
    'model' => $model,
    'attribute' => 'phone',
    'countries' => ['ru', 'gb'] // you can use following array to determine which countries you want to show
    'buttonOptions' => []
]); ?>
```

* Search widget (geocomplete.js)
Widget represents wrapper for [geocomplete](https://github.com/ubilabs/geocomplete)
Renders input, label (if needed) and map if property $enableMap is true (set by default)
Usage:
It has 2 use cases:
- Populate field's data by from available list (full list is shown [here](http://ubilabs.github.io/geocomplete/examples/form.html)) when `$enableForm` is true.
By default widget looking in data-geo attribute instead name
- Just using geocomplete without populating field data

Example:

```
Search::widget([
    'enableMap' => false,
    'containerId' => $id,
    'options' => [
        'id' => 'geocomplete',
        'class' => 'form-control',
        'placeholder' => Yii::$app->translate->t('Enter a postal code or a place name')
    ],
    'label' => Yii::$app->translate->t('Address search')
]);
```

* Flag icon usage

```
use dbsparkle-team\unispot\assets\FlagIconAsset;
use dbsparkle-team\unispot\components\FlagIcon;
echo FlagIcon::flag('RU');
```

* Location::getMapUrl usage

```
$url = Location::getMapUrl(60.023554, 30.2232882, [
    'type' => self::MAP_TYPE_GOOGLE
]);
echo Html::a('link', $url);
```

* Location::getAddress usage

```
use \dbsparkle-team\unispot\formatters\AddressFormatter;
use \dbsparkle-team\unispot\components\Location;

Location::getAddress(60.023554, 30.2232882,[
    'type' => AddressFormatter::TYPE_FULL,
    'format' => AddressFormatter::FORMAT_NORMAL
])
```

CONTRIBUTORS
-----------
* Dmitry Fedorov <klka1@live.ru>
* Doszhan
* Mark Song
* Andrey Andreev
* Anton Filippov
* Domi Besedin <dimbs09@gmail.com>

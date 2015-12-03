# Repository for rebecca-app widgets
##Widgets

- PhoneInput
- PhoneNumber

## PhoneInput widget for Yii2

Creates a masked input for phone number with a dropdown for selecting a country code.

### Usage

```
// on your view
<?php 
use dbsparkle-team\unispot\widgets\phone\PhoneInput;
echo PhoneInput::widget([
    'model' => $model,
    'attribute' => 'phone',
    'countries' => ['ru', 'gb'], // you can use following array to determine which countries you want to show
    'buttonOptions' => [],
    'useDropdown' => true // it forces to use dropdowns, by default it is false
]);
?>
```

## PhoneNumber widget for Yii2

PhoneNumber is a widget wrapper to show formatted phone with tel: link and country flag.

###Usage

```
// on your view
<?php 
use dbsparkle-team\unispot\widgets\phone\PhoneNumber;
echo PhoneNumber::widget([
	'model' => $model,
	'format' => PhoneNumber::INTERNATIONAL // if format is not set, then phone of your country will be formatted in national instead international format
]);
?>
```
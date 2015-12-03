<?php

/**
 * @var $widget dbsparkle-team\unispot\widgets\phone\PhoneInput
 */
use yii\bootstrap\ButtonDropdown;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\MaskedInput;

$buttonDropdown = ButtonDropdown::widget([
    'label' => $widget->buttonOptions['label'],
    'options' => $widget->buttonOptions['options'],
    'dropdown' => $widget->dropdown,
]);

$widgetConfig = [
    'name' => $widget->name,
    'value' => $widget->value,
    'mask' => $widget->mask,
    'options' => ArrayHelper::merge($widget->options, [
        'class' => 'form-control ' . $s['class']['inputMask']
    ]),
    'clientOptions' => [
        'alias' => ' ',
        'placeholder' => ' '
    ]
];

if (!$widget->form) {
    $widgetConfig = array_merge(
        $widgetConfig,
        [
            'model' => $widget->model,
            'attribute' => $widget->attribute,
        ]
    );
}
?>

<div
    class="row <?= $s['class']['widgetContainer'] ?>"
    data-country-code-id="<?= $widget->getInputId('country_code') ?>">

    <div class="<?= $s['class']['col'] ?>">

        <?= $widget->label ? Html::activeLabel($widget->model, $widget->attribute) : '' ?>

            <?php if ($widget->form): ?>

                <?= $widget
                    ->form
                    ->field($widget->model, $widget->attribute, [
                        'addon' => ['prepend' => ['content'=>$buttonDropdown, 'asButton' => true]]
                    ])
                    ->widget(MaskedInput::className(), $widgetConfig)
                    ->label(false)
                ?>

            <?php else: ?>

                <div class="input-group" >
                    <span class="input-group-btn"><?= $buttonDropdown ?></span>
                    <?= MaskedInput::widget($widgetConfig) ?>
                </div>

                <?php if ($widget->hint): ?>

                    <?= Html::activeHint($widget->model, $widget->attribute, [
                        'hint' => $widget->hint
                    ]); ?>

                <?php endif; ?>

            <?php endif; ?>

    </div>
</div>

<?php
$this->registerCss('
.phone-org-form .dropdown-menu {
    max-height: 390px;
    overflow-y: scroll;
}
');

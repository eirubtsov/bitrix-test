<?php

declare(strict_types=1);

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>
<?php if (!empty($arResult['WEATHER'])): ?>
    <p><?= Loc::getMessage('WEATHER_WIDGET_TEMPLATE.CITY', ['#VALUE#' => $arParams['CITY']]) ?></p>
    <?php foreach ($arResult['WEATHER'] as $key => $value): ?>
        <p><?= Loc::getMessage("WEATHER_WIDGET_TEMPLATE.{$key}", ['#VALUE#' => $value]) ?></p>
    <?php endforeach; ?>
<?php endif; ?>

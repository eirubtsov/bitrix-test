<?php

declare(strict_types=1);


use Weather\Forecast\Helpers\NumberHelper;

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

if (!empty($arResult['WEATHER'])) {
    foreach ($arResult['WEATHER'] as $key => $value) {
        // Округляем значения
        $value = (int)round($value);

        // Добавляем знак "+" для положительной температуры
        if ($key === 'temperature') {
            $value = NumberHelper::toSignedString($value);
        }

        $arResult['WEATHER'][$key] = $value;
    }
}

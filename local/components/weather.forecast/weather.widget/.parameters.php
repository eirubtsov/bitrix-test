<?php

declare(strict_types=1);

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Weather\Forecast\Enums\UnitsEnum;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

Loader::includeModule('weather.forecast');

// Собираем значения допустимых метрик из enum.
$unitValues = [];
foreach (UnitsEnum::cases() as $case) {
    $unitValues[$case->value] = $case->description();
}

$arComponentParameters = [
    'GROUPS' => [],
    'PARAMETERS' => [
        'CACHE_TIME' => [
            'DEFAULT' => 1800,
        ],
        'CITY' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('WEATHER_WIDGET_PARAM.CITY'),
            'TYPE' => 'STRING',
            'DEFAULT' => 'Moscow',
        ],
        'UNITS' => [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('WEATHER_WIDGET_PARAM.UNITS'),
            'TYPE' => 'LIST',
            'VALUES' => $unitValues,
            'DEFAULT' => UnitsEnum::METRIC->value,
            'MULTIPLE' => 'N',
            'ADDITIONAL_VALUES' => 'N',
            'REFRESH' => 'N',
        ],
    ],
];

<?php

declare(strict_types=1);

use Rubtsov\Gadget\Helpers\ModuleHelper;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

// Имя папки = ID модуля
$moduleId = basename(dirname(__DIR__));
define('WEATHER_FORECAST_MODULE_ID', $moduleId);

// Абсолютный путь до модуля
define('WEATHER_FORECAST_MODULE_PATH', realpath(__DIR__ . '/../'));
// Относительный путь до модуля
define(
    'WEATHER_FORECAST_MODULE_PATH_RELATIVE',
    ModuleHelper::getModulePathRelative(WEATHER_FORECAST_MODULE_PATH)
);

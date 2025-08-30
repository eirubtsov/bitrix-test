<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Bitrix\Main\Routing\RoutingConfigurator;
use Weather\Forecast\Controllers\WeatherController;

return static function (RoutingConfigurator $routes) {
    $routes->prefix('api')->group(function (RoutingConfigurator $routes) {
        $routes->prefix('v1')->group(function (RoutingConfigurator $routes) {
            $routes->prefix('weather')->group(function (RoutingConfigurator $routes) {
                Loader::includeModule('weather.forecast');
                $routes->get(
                    'getCurrentWeather/',
                    [WeatherController::class, 'getCurrentWeather']
                );
            });
        });
    });
};

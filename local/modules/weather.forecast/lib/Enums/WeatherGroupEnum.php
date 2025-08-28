<?php

declare(strict_types=1);

namespace Weather\Forecast\Enums;

/**
 * Группа погодных параметров из OpenWeather (поле weather.main).
 */
enum WeatherGroupEnum: string
{
    case Thunderstorm = 'Thunderstorm';
    case Drizzle = 'Drizzle';
    case Rain = 'Rain';
    case Snow = 'Snow';
    case Clear = 'Clear';
    case Clouds = 'Clouds';
    case Mist = 'Mist';
    case Smoke = 'Smoke';
    case Haze = 'Haze';
    case Dust = 'Dust';
    case Fog = 'Fog';
    case Sand = 'Sand';
    case Ash = 'Ash';
    case Squall = 'Squall';
    case Tornado = 'Tornado';
}

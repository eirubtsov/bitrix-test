<?php

declare(strict_types=1);

namespace Weather\Forecast\Facades;

use Weather\Forecast\Api\Clients\CurrentWeatherClient;
use Weather\Forecast\DTOs\ApiResultDTO;

class WeatherForecast
{
    /**
     * Получение информации о текущей погоде
     * @param string|null $query
     * @param int|null $lat
     * @param int|null $lon
     * @param string|null $units
     * @return ApiResultDTO
     */
    public static function getCurrentWeather(
        ?string $query = '',
        ?int $lat = null,
        ?int $lon = null,
        ?string $units = null
    ): ApiResultDTO
    {
        $client = new CurrentWeatherClient();
        return $client->getCurrentWeather(
            $query,
            $lat,
            $lon,
            $units
        );
    }
}

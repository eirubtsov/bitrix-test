<?php

declare(strict_types=1);

namespace Weather\Forecast\Facades;

use Bitrix\Rest\RestException;
use Weather\Forecast\Api\Clients\CurrentWeatherClient;
use Weather\Forecast\DTOs\ApiResultDTO;
use Weather\Forecast\DTOs\Weather\ShortWeatherDTO;
use Weather\Forecast\Services\WeatherService;

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
    ): ApiResultDTO {
        $client = new CurrentWeatherClient();
        return $client->getCurrentWeather(
            $query,
            $lat,
            $lon,
            $units
        );
    }

    /**
     * Получение короткой сводки о текущей погоде
     * @param string|null $query
     * @param int|null $lat
     * @param int|null $lon
     * @param string|null $units
     * @return ShortWeatherDTO
     * @throws RestException
     */
    public static function getShortCurrentWeather(
        ?string $query = '',
        ?int $lat = null,
        ?int $lon = null,
        ?string $units = null
    ): ShortWeatherDTO {
        $weatherService = new WeatherService();
        return $weatherService->getShortCurrentWeather(
            $query,
            $lat,
            $lon,
            $units
        );
    }
}

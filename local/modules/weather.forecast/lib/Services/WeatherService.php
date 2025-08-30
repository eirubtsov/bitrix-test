<?php

declare(strict_types=1);

namespace Weather\Forecast\Services;

use Bitrix\Main\Localization\Loc;
use Bitrix\Rest\RestException;
use Weather\Forecast\DTOs\Weather\CurrentWeatherDTO;
use Weather\Forecast\DTOs\Weather\ShortWeatherDTO;
use Weather\Forecast\Facades\WeatherForecast;

class WeatherService
{
    private const float HPA_TO_MMHG = 0.75006;

    /**
     * Получение короткой сводки о погоде
     * @param string|null $query
     * @param int|null $lat
     * @param int|null $lon
     * @param string|null $units
     * @return mixed
     * @throws RestException
     */
    public function getShortCurrentWeather(
        ?string $query = '',
        ?int $lat = null,
        ?int $lon = null,
        ?string $units = null
    ): ShortWeatherDTO {
        $response = WeatherForecast::getCurrentWeather($query, $lat, $lon, $units);

        // Если ответ не успешный
        if (!$response->isSuccess()) {
            // Получаем текст ошибки
            $message = !empty($response->context['errorData']['message'])
                ? $response->context['errorData']['message']
                : Loc::getMessage('ERROR_RECEIVING_WEATHER_DATA');
            throw new RestException(
                $message,
                $response->context['statusCode']
            );
        }

        /** @var CurrentWeatherDTO $currentWeatherDTO */
        $currentWeatherDTO = $response->data;

        return new ShortWeatherDTO(
            $currentWeatherDTO->main->temp,
            $currentWeatherDTO->main->humidity,
            static::hPaToMmHg($currentWeatherDTO->main->grndLevel)
        );
    }

    /**
     * Переводит давление из гектопаскалей (гПа) в миллиметры ртутного столба (мм рт. ст.).
     * @param float|int $hPa Давление в гПа
     * @return float Давление в мм рт. ст.
     */
    public static function hPaToMmHg(float|int $hPa): float
    {
        return round($hPa * self::HPA_TO_MMHG, 2);
    }
}

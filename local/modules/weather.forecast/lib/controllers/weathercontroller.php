<?php

declare(strict_types=1);

namespace Weather\Forecast\Controllers;

use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\HttpMethod;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Exception;
use Weather\Forecast\Facades\WeatherForecast;
use Weather\Forecast\Helpers\CacheHelper;

class WeatherController extends Controller
{
    /**
     * @return array
     */
    public function configureActions(): array
    {
        $actionsConfig['getCurrentWeather'] = [
            'prefilters' => [
                // Дополнительно разрешаем метод POST ак как он используется в BX.ajax.runAction()
                new HttpMethod([HttpMethod::METHOD_GET, HttpMethod::METHOD_POST]),
            ],
            '-prefilters' => [
                Authentication::class,
                Csrf::class,
            ],
        ];

        return $actionsConfig;
    }

    /**
     * Получение короткой сводки о текущей погоде
     * @param string|null $query
     * @param int|null $lat
     * @param int|null $lon
     * @param string|null $units
     * @return array
     */
    public function getCurrentWeatherAction(
        ?string $query = '',
        ?int $lat = null,
        ?int $lon = null,
        ?string $units = null
    ): array {
        $data = [];

        try {
            // Формируем уникальный ключ для кеша на основе входных параметров метода
            $cacheKey = md5(json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

            // Получение данных с кешированием
            $data = CacheHelper::remember($cacheKey, 1800, function () use ($query, $lat, $lon, $units) {
                // Получение короткой сводки о погоде
                return WeatherForecast::getShortCurrentWeather(
                    $query,
                    $lat,
                    $lon,
                    $units
                )?->toArray() ?: [];
            });
        } catch (Exception $e) {
            // TODO: Добавить логирование
            $responseCode = $e->getCode() ?: 500;
            http_response_code($responseCode);
            $this->addError(new Error($e->getMessage(), $responseCode));
        }

        return $data;
    }
}

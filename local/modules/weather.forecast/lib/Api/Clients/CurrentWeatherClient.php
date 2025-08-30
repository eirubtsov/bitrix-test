<?php

declare(strict_types=1);

namespace Weather\Forecast\Api\Clients;

use Exception;
use Weather\Forecast\DTOs\ApiResultDTO;
use Weather\Forecast\DTOs\RequestOptionsDTO;
use Weather\Forecast\DTOs\Weather\CurrentWeatherDTO;
use Weather\Forecast\Helpers\ApiResultHelper;

/**
 * Работа с данными о текущей погоде
 */
class CurrentWeatherClient extends AbstractApiClient
{
    /**
     * Получение информации о текущей погоде
     * @param string|null $query
     * @param int|null $lat
     * @param int|null $lon
     * @param string|null $units
     * @return ApiResultDTO
     */
    public function getCurrentWeather(
        ?string $query = '',
        ?int $lat = null,
        ?int $lon = null,
        ?string $units = null
    ): ApiResultDTO {
        $endpoint = '/data/2.5/weather';

        try {
            $options = new RequestOptionsDTO();

            if (!empty($query)) {
                $options->withQuery('q', $query);
            }
            if (!empty($lat)) {
                $options->withQuery('lat', $lat);
            }
            if (!empty($lon)) {
                $options->withQuery('lon', $lon);
            }
            if (!empty($units)) {
                $options->withQuery('units', $units);
            }

            $response = $this->request('POST', $endpoint, $options);

            if (!$response->isSuccess()) {
                return ApiResultHelper::buildErrorResult($response, [
                    'endpoint' => $endpoint,
                    'errorData' => $response->data,
                ]);
            }

            $data = $response->data ?? [];

            return new ApiResultDTO(
                success: true,
                data: CurrentWeatherDTO::fromArray($data),
                context: [
                    'status' => $response->statusCode ?: null,
                    'duration' => $response->duration ?: null,
                    'endpoint' => $endpoint,
                ]
            );
        } catch (Exception $exception) {
            return ApiResultHelper::buildExceptionResult($exception, [
                'endpoint' => $endpoint,
            ]);
        }
    }
}

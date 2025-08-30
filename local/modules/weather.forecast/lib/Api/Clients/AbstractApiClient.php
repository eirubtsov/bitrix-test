<?php

declare(strict_types=1);

namespace Weather\Forecast\Api\Clients;

use Bitrix\Main\Config\Option;
use ReflectionException;
use RuntimeException;
use Weather\Forecast\Api\Http\Transport\BitrixTransport;
use Weather\Forecast\Contracts\Api\TransportInterface;
use Weather\Forecast\DTOs\ApiResponseDTO;
use Weather\Forecast\DTOs\RequestOptionsDTO;

abstract class AbstractApiClient
{
    /**
     * Единый для всех методов транспорт
     * @var TransportInterface
     */
    protected TransportInterface $transport;
    /**
     * Базовый url API
     * @var string
     */
    protected string $baseUrl;

    /**
     * Ключ API
     * @var string
     */
    protected string $apiKey;

    public function __construct()
    {
        // Жёстко задаём транспорт для всех методов
        $this->transport = new BitrixTransport();

        // Получаем конфиг из опций модуля
        $this->baseUrl = rtrim(Option::get(WEATHER_FORECAST_MODULE_ID, 'api_base_url'), '/');
        $this->apiKey = Option::get(WEATHER_FORECAST_MODULE_ID, 'api_key');

        if (empty($this->baseUrl)) {
            throw new RuntimeException(
                'Параметр "Базовый url API" не настроен для модуля ' . WEATHER_FORECAST_MODULE_ID
            );
        }
        if (empty($this->apiKey)) {
            throw new RuntimeException(
                'Параметр "Ключ API" не настроен для модуля ' . WEATHER_FORECAST_MODULE_ID
            );
        }
    }

    /**
     * Унифицированный запрос с базовой конфигурацией
     * @param string $method
     * @param string $endpoint
     * @param RequestOptionsDTO|null $options
     * @return ApiResponseDTO
     * @throws ReflectionException
     */
    protected function request(
        string $method,
        string $endpoint,
        ?RequestOptionsDTO $options = null,
    ): ApiResponseDTO {
        $options = $options ?? new RequestOptionsDTO();
        $options->withHeader('Accept', 'application/json');
        $options->withHeader('Content-Type', 'application/json');

        $options->withQuery('apiKey', $this->apiKey);
        $options->withQuery('lang', 'ru');

        return $this->transport->request($method, $this->baseUrl . $endpoint, $options);
    }
}

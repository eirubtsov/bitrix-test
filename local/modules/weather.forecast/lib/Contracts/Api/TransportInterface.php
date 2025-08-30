<?php

declare(strict_types=1);

namespace Weather\Forecast\Contracts\Api;

use Weather\Forecast\DTOs\ApiResponseDTO;
use Weather\Forecast\DTOs\RequestOptionsDTO;

/**
 * Контракт для транспортного слоя HTTP-запросов к внешнему API.
 *
 * Определяет единый метод отправки HTTP-запроса, который должны реализовать
 * конкретные транспортные классы (например, на основе Bitrix HttpClient, cURL, Guzzle и т.д.).
 *
 * Принципы:
 *  - Иммутабельность входных DTO: реализация не должна модифицировать RequestOptionsDTO.
 *  - Возврат строго типизированного ApiResponseDTO.
 *  - Не обрабатывать бизнес-ошибки — только формировать ответ с кодом, данными, ошибками транспорта.
 */
interface TransportInterface
{
    /**
     * Выполняет HTTP-запрос к указанному URL с заданными параметрами.
     *
     * @param string $method HTTP-метод (GET, POST, PUT, DELETE и т.п.).
     * @param string $url Полный URL запроса (схема + хост + путь).
     * @param RequestOptionsDTO $options Объект с дополнительными параметрами
     * @return ApiResponseDTO Объект с информацией об ответе
     */
    public function request(
        string $method,
        string $url,
        RequestOptionsDTO $options = new RequestOptionsDTO()
    ): ApiResponseDTO;
}

<?php

declare(strict_types=1);

namespace Weather\Forecast\Api\Http\Transport;

use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Uri;
use Weather\Forecast\Contracts\Api\TransportInterface;
use Weather\Forecast\DTOs\ApiResponseDTO;
use Weather\Forecast\DTOs\RequestOptionsDTO;

/**
 * Транспорт для работы с внешними API на основе встроенного Bitrix HttpClient.
 */
final class BitrixTransport implements TransportInterface
{
    /** Таймаут по умолчанию, если не задан явно */
    private const float DEFAULT_TIMEOUT = 30.0;

    /** Флаги JSON-кодирования для тела запроса */
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;

    /**
     * Выполняет HTTP-запрос к API.
     * @param string $method HTTP-метод (GET, POST и т.п.)
     * @param string $url URL адрес запроса
     * @param RequestOptionsDTO $options Настройки запроса
     * @return ApiResponseDTO Стандартизированный ответ
     */
    public function request(
        string $method,
        string $url,
        RequestOptionsDTO $options = new RequestOptionsDTO()
    ): ApiResponseDTO {
        $method = strtoupper($method);
        $headers = $options->headers;
        $query = $options->query;
        $body = $options->body;
        $timeout = $options->timeout ?: self::DEFAULT_TIMEOUT;

        // Формируем URL с query-параметрами
        $uri = new Uri($url);
        if (!empty($query)) {
            $uri->addParams($query);
        }

        // Создаём HttpClient с нужными таймаутами и заголовками
        $client = new HttpClient([
            'socketTimeout' => $timeout,
            'streamTimeout' => $timeout,
        ]);
        foreach ($headers as $name => $value) {
            $client->setHeader((string)$name, $value);
        }

        // Готовим тело запроса
        // Если явно указан Content-Type: application/json и body — массив/объект,
        // то кодируем его в строку JSON. В остальных случаях передаём как есть.
        $lower = array_change_key_case($headers);
        $payload = $body;
        if (isset($lower['content-type']) && str_contains($lower['content-type'], 'application/json')) {
            if (is_array($body) || is_object($body)) {
                $payload = json_encode($body, self::JSON_FLAGS);
            }
        }

        // Выполняем запрос и замеряем время
        $start = microtime(true);
        $raw = $client->query($method, (string)$uri, $payload) ? $client->getResult() : '';
        $took = round(microtime(true) - $start, 6);

        // Преобразуем ответ в DTO
        return $this->buildResponse($client, $raw, $options->expectJson, $took);
    }

    /**
     * Преобразует результат работы HttpClient в объект-DTO ApiResponseDTO.
     * @param HttpClient $client Экземпляр HttpClient, выполнивший запрос
     * @param string $raw Сырое тело ответа
     * @param bool $expectJson Нужно ли пытаться парсить ответ как JSON
     * @param float $duration Длительность запроса (сек.)
     * @return ApiResponseDTO
     */
    private function buildResponse(HttpClient $client, string $raw, bool $expectJson, float $duration): ApiResponseDTO
    {
        // HTTP-статус
        $status = $client->getStatus();

        // Заголовки ответа
        $headers = $client->getHeaders()?->toArray() ?? [];

        // Сетевые/низкоуровневые ошибки транспорта
        $netErrors = $client->getError();

        // Парсим тело, если ожидаем JSON; иначе возвращаем как строку
        $data = $expectJson
            ? (json_decode($raw, true) ?? ['raw' => $raw])
            : ['raw' => $raw];

        // Успех = статус 2xx и отсутствие сетевых ошибок
        $success = ($status >= 200 && $status < 300) && empty($netErrors);

        // Нормализуем ошибки (в формате, совместимом с ApiResponseDTO)
        $errors = [];
        foreach ($netErrors as $code => $msg) {
            $errors[] = ['code' => $code, 'message' => (string)$msg, 'details' => null];
        }
        // Если запрос неуспешен, но сетевых ошибок нет — добавляем дефолтную http-ошибку
        if (!$success && empty($errors)) {
            $errors[] = ['code' => 'http_error', 'message' => 'HTTP error', 'details' => null];
        }

        // Собираем итоговый объект ответа
        return new ApiResponseDTO(
            $status,
            $success,
            $data,
            $errors,
            $headers,
            $raw,
            $duration,
        );
    }
}

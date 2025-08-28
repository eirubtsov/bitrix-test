<?php

declare(strict_types=1);

namespace Weather\Forecast\DTOs;

/**
 * DTO для хранения унифицированного ответа от внешнего API.
 */
final class ApiResponseDTO extends AbstractDTO
{
    /**
     * Конструктор объекта ответа API.
     * @param int $statusCode HTTP-код ответа.
     * @param bool $success Флаг успешности запроса (обычно true для кодов 2xx).
     * @param array $data Декодированные данные ответа (если JSON) или структура с ключом 'raw'.
     * @param array $errors Массив ошибок (код, сообщение, детали).
     * @param array $headers Заголовки ответа в виде ассоциативного массива.
     * @param string|null $rawBody Необработанное тело ответа (JSON-строка или HTML и т.д.).
     * @param float|null $duration Время выполнения запроса в секундах.
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly bool $success,
        public readonly array $data = [],
        public readonly array $errors = [],
        public readonly array $headers = [],
        public readonly ?string $rawBody = null,
        public readonly ?float $duration = null
    ) {
    }

    /**
     * Проверяет, был ли запрос успешным.
     * @return bool true, если запрос выполнился успешно.
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Проверяет наличие ошибок в ответе.
     * @return bool true, если есть хотя бы одна ошибка.
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}

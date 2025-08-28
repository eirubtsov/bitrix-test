<?php

declare(strict_types=1);

namespace Weather\Forecast\DTOs;

/**
 * DTO для передачи параметров HTTP-запроса
 * в транспортный слой API-клиента.
 *
 * Используется для настройки запроса перед его выполнением:
 *  - добавления/удаления заголовков
 *  - указания GET-параметров
 *  - задания тела запроса
 *  - управления таймаутом
 *  - указания, что ожидается JSON-ответ
 *
 * Принципы:
 *  - Методы `with*` изменяют текущий объект (мутабельный подход).
 *  - Может переиспользоваться между запросами с модификацией.
 */
final class RequestOptionsDTO extends AbstractDTO
{
    /**
     * @param array<string,string> $headers Ассоциативный массив HTTP-заголовков
     * @param array<string,scalar|array|null> $query GET-параметры запроса
     * @param mixed $body Тело запроса (массив, строка, объект и т.п.)
     * @param float $timeout Таймаут запроса в секундах
     * @param bool $expectJson Признак того, что ответ ожидается в формате JSON
     */
    public function __construct(
        public array $headers = [],
        public array $query = [],
        public mixed $body = null,
        public float $timeout = 30.0,
        public bool $expectJson = true
    ) {
    }

    /**
     * Добавляет или заменяет HTTP-заголовок.
     *
     * @param string $name Имя заголовка (например, Authorization)
     * @param string $value Значение заголовка
     * @return $this
     */
    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Удаляет HTTP-заголовок.
     *
     * @param string $name Имя заголовка
     * @return $this
     */
    public function withoutHeader(string $name): self
    {
        unset($this->headers[$name]);
        return $this;
    }

    /**
     * Добавляет или заменяет GET-параметр запроса.
     *
     * @param string $name Имя параметра
     * @param string|int|float|bool|array|null $value Значение параметра
     * @return $this
     */
    public function withQuery(string $name, string|int|float|bool|null|array $value): self
    {
        $this->query[$name] = $value;
        return $this;
    }

    /**
     * Удаляет GET-параметр запроса.
     *
     * @param string $name Имя параметра
     * @return $this
     */
    public function withoutQuery(string $name): self
    {
        unset($this->query[$name]);
        return $this;
    }

    /**
     * Устанавливает тело запроса.
     *
     * @param mixed $body Данные для тела запроса (например, массив для JSON)
     * @return $this
     */
    public function withBody(mixed $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Устанавливает таймаут запроса.
     *
     * @param float $timeout Таймаут в секундах
     * @return $this
     */
    public function withTimeout(float $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Устанавливает, что ответ ожидается в формате JSON.
     *
     * @param bool $expect true — если ожидается JSON, false — если нет
     * @return $this
     */
    public function withExpectJson(bool $expect): self
    {
        $this->expectJson = $expect;
        return $this;
    }
}

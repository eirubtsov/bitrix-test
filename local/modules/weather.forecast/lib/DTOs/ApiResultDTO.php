<?php

declare(strict_types=1);

namespace Weather\Forecast\DTOs;

/**
 * Универсальный результат вызова API
 */
final class ApiResultDTO extends AbstractDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly mixed $data = null,
        public readonly ?string $message = null,
        /** @var array<int|string,mixed> */
        public readonly array $errors = [],
        /** @var array<int|string,mixed> */
        public readonly array $context = [],
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
}

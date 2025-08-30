<?php

declare(strict_types=1);

namespace Weather\Forecast\Helpers;

use Throwable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class ExceptionHelper
{
    /**
     * Унифицированный возврат обработанной ошибки
     * @param Throwable $exception
     * @param array $additionalContext
     * @return array
     */
    public static function buildExceptionResult(Throwable $exception, array $additionalContext = []): array
    {
        $context = [
            'status' => $exception->getCode() ?: 500,
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
            'codeLine' => "{$exception->getFile()}:{$exception->getLine()}",
        ];

        return array_merge($context, $additionalContext);
    }
}

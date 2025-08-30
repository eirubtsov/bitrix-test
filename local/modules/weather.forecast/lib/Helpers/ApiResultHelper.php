<?php

declare(strict_types=1);

namespace Weather\Forecast\Helpers;

use Weather\Forecast\DTOs\ApiResponseDTO;
use Weather\Forecast\DTOs\ApiResultDTO;
use Throwable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class ApiResultHelper
{
    /**
     * Унифицированный возврат неуспешного результата запроса
     * @param ApiResponseDTO $response
     * @param array $additionalContext
     * @param string $message
     * @return ApiResultDTO
     */
    public static function buildErrorResult(
        ApiResponseDTO $response,
        array $additionalContext = [],
        string $message = 'Ошибка запроса',
    ): ApiResultDTO {
        $context = [
            'statusCode' => $response->statusCode ?? null,
            'duration' => $response->duration ?? null,
        ];
        $context = array_merge($context, $additionalContext);

        return new ApiResultDTO(
            success: false,
            message: $message,
            errors: $response->errors ?? [],
            context: $context
        );
    }

    /**
     * Унифицированный возврат неуспешного из-за исключения результата запроса
     * @param Throwable $exception
     * @param array $additionalContext
     * @return ApiResultDTO
     */
    public static function buildExceptionResult(Throwable $exception, array $additionalContext = []): ApiResultDTO
    {
        $context = [
            'statusCode' => $exception->getCode() ?: 500,
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
            'file' => "{$exception->getFile()}:{$exception->getLine()}",
        ];

        $context = array_merge($context, $additionalContext);

        return new ApiResultDTO(
            success: false,
            message: $exception->getMessage(),
            errors: [['exception' => $exception::class]],
            context: $context
        );
    }
}

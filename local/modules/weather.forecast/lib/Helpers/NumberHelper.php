<?php

declare(strict_types=1);

namespace Weather\Forecast\Helpers;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class NumberHelper
{
    /**
     * Преобразовывает число в строку с обязательным знаком
     * @param int|float $number
     * @return string
     */
    public static function toSignedString(int|float $number): string
    {
        if ($number > 0) {
            return "+{$number}";
        }

        // Для 0 и отрицательных значений ничего не делаем
        return (string)$number;
    }
}

<?php

declare(strict_types=1);

namespace Weather\Forecast\Enums;

/**
 * Единицы измерения для OpenWeatherMap API.
 */
enum UnitsEnum: string
{
    case STANDARD = 'standard';
    case METRIC = 'metric';
    case IMPERIAL = 'imperial';

    /**
     * Описание значений
     * @return string
     */
    public function description(): string
    {
        return match ($this) {
            self::STANDARD => 'Стандартные единицы (K, м/с, гПа)',
            self::METRIC => 'Метрическая система (°C, м/с, гПа)',
            self::IMPERIAL => 'Имперская система (°F, мили/ч, гПа)',
        };
    }
}

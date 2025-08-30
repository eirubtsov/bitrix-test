<?php

declare(strict_types=1);

namespace Weather\Forecast\DTOs\Weather;

use Weather\Forecast\DTOs\AbstractDTO;

/**
 * Погодное состояние.
 */
final class ShortWeatherDTO extends AbstractDTO
{
    /**
     * @param float $temperature Температура
     * @param int $humidity Влажность %
     * @param float $groundLevelPressureMmHg Атмосферное давление на уровне земли в миллиметры ртутного столба
     */
    public function __construct(
        public readonly float $temperature,
        public readonly int $humidity,
        public readonly float $groundLevelPressureMmHg,
    ) {
    }
}

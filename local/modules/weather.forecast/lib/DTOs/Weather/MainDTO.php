<?php

declare(strict_types=1);

namespace Weather\Forecast\DTOs\Weather;

use Weather\Forecast\DTOs\AbstractDTO;

/**
 * Основные метеопоказатели (температура, давление, влажность и т.п.).
 */
final class MainDTO extends AbstractDTO
{
    /**
     * @param float $temp
     * @param float $feelsLike
     * @param int $pressure
     * @param int $humidity
     * @param float|null $tempMin
     * @param float|null $tempMax
     * @param int|null $seaLevel
     * @param int|null $grndLevel
     */
    public function __construct(
        public readonly float $temp,
        public readonly float $feelsLike,
        public readonly int $pressure,
        public readonly int $humidity,
        public readonly ?float $tempMin = null,
        public readonly ?float $tempMax = null,
        public readonly ?int $seaLevel = null,
        public readonly ?int $grndLevel = null,
    ) {
    }

    /**
     * @return string[]
     */
    protected static function getFieldMap(): array
    {
        return [
            'feels_like' => 'feelsLike',
            'temp_min' => 'tempMin',
            'temp_max' => 'tempMax',
            'sea_level' => 'seaLevel',
            'grnd_level' => 'grndLevel',
        ];
    }
}

<?php

declare(strict_types=1);

namespace Weather\Forecast\DTOs\Weather;

use Weather\Forecast\DTOs\AbstractDTO;

/**
 * Координаты локации (долгота/широта).
 */
final class CoordinateDTO extends AbstractDTO
{
    /**
     * @param float $lon
     * @param float $lat
     */
    public function __construct(
        public readonly float $lon,
        public readonly float $lat,
    ) {
    }
}

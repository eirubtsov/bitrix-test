<?php

declare(strict_types=1);

namespace Weather\Forecast\DTOs\Weather;

use Weather\Forecast\DTOs\AbstractDTO;

/**
 * Параметры ветра.
 */
final class WindDTO extends AbstractDTO
{
    /**
     * @param float $speed
     * @param int $deg
     * @param float|null $gust
     */
    public function __construct(
        public readonly float $speed,
        public readonly int $deg,
        public readonly ?float $gust = null,
    ) {
    }
}

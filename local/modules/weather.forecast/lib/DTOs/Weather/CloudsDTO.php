<?php

declare(strict_types=1);

namespace Weather\Forecast\DTOs\Weather;

use Weather\Forecast\DTOs\AbstractDTO;

/**
 * Облачность, %.
 */
final class CloudsDTO extends AbstractDTO
{
    /**
     * @param int $all
     */
    public function __construct(
        public readonly int $all,
    ) {
    }
}

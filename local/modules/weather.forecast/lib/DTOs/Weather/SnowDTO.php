<?php

declare(strict_types=1);

namespace Weather\Forecast\DTOs\Weather;

use Weather\Forecast\DTOs\AbstractDTO;

/**
 * Интенсивность снегопада за 1 час (мм/ч).
 */
final class SnowDTO extends AbstractDTO
{
    /**
     * @param float $hour
     */
    public function __construct(
        public readonly float $hour,
    ) {
    }

    /**
     * @return string[]
     */
    protected static function getFieldMap(): array
    {
        return [
            '1h' => 'hour',
        ];
    }
}

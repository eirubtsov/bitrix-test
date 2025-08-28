<?php

declare(strict_types=1);

namespace Weather\Forecast\DTOs\Weather;

use Weather\Forecast\DTOs\AbstractDTO;
use Weather\Forecast\Enums\WeatherGroupEnum;

/**
 * Погодное состояние.
 */
final class WeatherDTO extends AbstractDTO
{
    /**
     * @param int $id
     * @param WeatherGroupEnum $main
     * @param string $description
     * @param string $icon
     */
    public function __construct(
        public readonly int $id,
        public readonly WeatherGroupEnum $main,
        public readonly string $description,
        public readonly string $icon,
    ) {
    }

    /**
     * @return array
     */
    protected static function getCustomTransformers(): array
    {
        return [
            'main' => static function (WeatherGroupEnum|string $value): WeatherGroupEnum {
                if ($value instanceof WeatherGroupEnum) {
                    return $value;
                }
                return WeatherGroupEnum::from((string)$value);
            },
        ];
    }

    /**
     * @return array
     */
    protected static function getCustomSerializers(): array
    {
        return [
            'main' => static fn(WeatherGroupEnum $value) => $value->value,
        ];
    }
}

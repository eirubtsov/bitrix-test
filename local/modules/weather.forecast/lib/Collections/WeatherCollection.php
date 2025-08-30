<?php

declare(strict_types=1);

namespace Weather\Forecast\Collections;

use Weather\Forecast\DTOs\Weather\WeatherDTO;

/**
 * Коллекция элементов массива WeatherDTO[].
 *
 * @extends AbstractTypedCollection<WeatherDTO>
 */
final class WeatherCollection extends AbstractTypedCollection
{
    /**
     * @return string
     */
    protected function elementType(): string
    {
        return WeatherDTO::class;
    }

    /**
     * @param mixed $item
     * @param int|string $key
     * @return mixed
     */
    protected function serializeItem(mixed $item, int|string $key): mixed
    {
        /** @var WeatherDTO $item */
        return $item->toArray(true);
    }
}

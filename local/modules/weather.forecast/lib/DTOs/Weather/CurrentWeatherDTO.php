<?php

declare(strict_types=1);

namespace Weather\Forecast\DTOs\Weather;

use DateTimeImmutable;
use Weather\Forecast\Collections\WeatherCollection;
use Weather\Forecast\DTOs\AbstractDTO;

/**
 * Структура OpenWeather "Current weather data".
 */
final class CurrentWeatherDTO extends AbstractDTO
{
    /**
     * @param CoordinateDTO $coordinate
     * @param WeatherCollection $weather
     * @param string|null $base
     * @param MainDTO $main
     * @param int|null $visibility
     * @param WindDTO|null $wind
     * @param CloudsDTO|null $clouds
     * @param RainDTO|null $rain
     * @param SnowDTO|null $snow
     * @param DateTimeImmutable $dt
     * @param SysDTO|null $sys
     * @param int $timezone
     * @param int $id
     * @param string $name
     * @param int $cod
     */
    public function __construct(
        public readonly CoordinateDTO $coordinate,
        public readonly WeatherCollection $weather,
        public readonly ?string $base,
        public readonly MainDTO $main,
        public readonly ?int $visibility,
        public readonly ?WindDTO $wind,
        public readonly ?CloudsDTO $clouds,
        public readonly ?RainDTO $rain,
        public readonly ?SnowDTO $snow,
        public readonly DateTimeImmutable $dt,
        public readonly ?SysDTO $sys,
        public readonly int $timezone,
        public readonly int $id,
        public readonly string $name,
        public readonly int $cod,
    ) {
    }

    /**
     * @return string[]
     */
    protected static function getFieldMap(): array
    {
        return [
            'coord' => 'coordinate',
        ];
    }

    /**
     * Трансформируем UNIX-время и собираем вложенные структуры.
     *  Для DTO не нужны сериализаторы — базовый AbstractDTO сам вызовет toArray().
     *  Для коллекции weather нужен отдельный сериализатор (ниже).
     * @return array
     */
    protected static function getCustomTransformers(): array
    {
        return [
            'dt' => static fn(mixed $v): DateTimeImmutable => (new DateTimeImmutable())->setTimestamp((int)$v),
            'weather' => static function (mixed $v): WeatherCollection {
                if ($v instanceof WeatherCollection) {
                    return $v;
                }
                $items = [];
                foreach ((array)$v as $row) {
                    $items[] = $row instanceof WeatherDTO ? $row : WeatherDTO::fromArray((array)$row);
                }
                return WeatherCollection::from($items);
            },
        ];
    }

    /**
     * Доп. сериализация нужна только для коллекции weather,
     * т.к. она не является DTO и базовый класс не знает, как её развернуть.
     * Для остальных вложенных DTO кастомные сериализаторы не нужны.
     * @return array
     */
    protected static function getCustomSerializers(): array
    {
        return [
            'dt' => static fn(DateTimeImmutable $d) => $d->getTimestamp(),
            'weather' => static fn(WeatherCollection $c) => $c->toArray(),
        ];
    }
}

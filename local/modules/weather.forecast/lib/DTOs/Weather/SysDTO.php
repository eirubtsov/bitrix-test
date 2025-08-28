<?php

declare(strict_types=1);

namespace Weather\Forecast\DTOs\Weather;

use DateTimeImmutable;
use Weather\Forecast\DTOs\AbstractDTO;

/**
 * Системные/внутренние поля, а также времена рассвета/заката (UNIX -> DateTimeImmutable).
 */
final class SysDTO extends AbstractDTO
{
    /**
     * @param int|null $type
     * @param int|null $id
     * @param float|null $message
     * @param string|null $country
     * @param DateTimeImmutable|null $sunrise
     * @param DateTimeImmutable|null $sunset
     */
    public function __construct(
        public readonly ?int $type = null,
        public readonly ?int $id = null,
        public readonly ?float $message = null,
        public readonly ?string $country = null,
        public readonly ?DateTimeImmutable $sunrise = null,
        public readonly ?DateTimeImmutable $sunset = null,
    ) {
    }

    /**
     * @return array
     */
    protected static function getCustomTransformers(): array
    {
        return [
            'sunrise' => static function (mixed $v): ?DateTimeImmutable {
                return $v === null ? null : (new DateTimeImmutable())->setTimestamp((int)$v);
            },
            'sunset' => static function (mixed $v): ?DateTimeImmutable {
                return $v === null ? null : (new DateTimeImmutable())->setTimestamp((int)$v);
            },
        ];
    }

    /**
     * @return array
     */
    protected static function getCustomSerializers(): array
    {
        return [
            'sunrise' => static fn(?DateTimeImmutable $d) => $d?->getTimestamp(),
            'sunset' => static fn(?DateTimeImmutable $d) => $d?->getTimestamp(),
        ];
    }
}

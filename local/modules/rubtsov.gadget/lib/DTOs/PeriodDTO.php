<?php

declare(strict_types=1);

namespace Rubtsov\Gadget\DTOs;

use Bitrix\Main\Type\DateTime;

/**
 * Временное окно периода с началом и концом.
 */
final readonly class PeriodDTO
{
    /**
     * @param DateTime $from
     * @param DateTime $to
     */
    public function __construct(
        public DateTime $from,
        public DateTime $to,
    ) {
    }
}

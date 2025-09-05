<?php

declare(strict_types=1);

namespace Rubtsov\Gadget\DTOs\Application;

use Rubtsov\Gadget\DTOs\PeriodDTO;

/**
 * Набор периодов для формирования статистики по заявкам.
 */
final readonly class ApplicationPeriodStatisticBoundsDTO
{
    /**
     * @param PeriodDTO $today
     * @param PeriodDTO $week
     * @param PeriodDTO $month
     */
    public function __construct(
        public PeriodDTO $today,
        public PeriodDTO $week,
        public PeriodDTO $month,
    ) {
    }
}

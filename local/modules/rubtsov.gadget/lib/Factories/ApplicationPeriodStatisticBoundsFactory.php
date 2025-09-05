<?php

declare(strict_types=1);

namespace Rubtsov\Gadget\Factories;

use Bitrix\Main\ObjectException;
use Bitrix\Main\Type\DateTime;
use DateMalformedStringException;
use DateTimeImmutable;
use Rubtsov\Gadget\DTOs\Application\ApplicationPeriodStatisticBoundsDTO;
use Rubtsov\Gadget\DTOs\PeriodDTO;

class ApplicationPeriodStatisticBoundsFactory
{
    /**
     * Формирование периодов для сбора статистики
     * @return ApplicationPeriodStatisticBoundsDTO
     * @throws ObjectException
     * @throws DateMalformedStringException
     */
    public static function make(): ApplicationPeriodStatisticBoundsDTO
    {
        $now = new DateTimeImmutable();
        $format = 'd.m.Y H:i:s';

        $todayFrom = new DateTime($now->setTime(0, 0, 0)->format($format));
        $todayTo = new DateTime($now->setTime(23, 59, 59)->format($format));

        $monday = $now->modify('monday this week')->setTime(0, 0, 0);
        $sunday = $now->modify('sunday this week')->setTime(23, 59, 59);
        $weekFrom = new DateTime($monday->format($format));
        $weekTo = new DateTime($sunday->format($format));

        $first = $now->modify('first day of this month')->setTime(0, 0, 0);
        $last = $now->modify('last day of this month')->setTime(23, 59, 59);
        $monthFrom = new DateTime($first->format($format));
        $monthTo = new DateTime($last->format($format));

        return new ApplicationPeriodStatisticBoundsDTO(
            new PeriodDTO($todayFrom, $todayTo),
            new PeriodDTO($weekFrom, $weekTo),
            new PeriodDTO($monthFrom, $monthTo),
        );
    }
}

<?php

declare(strict_types=1);

namespace Rubtsov\Gadget\Enums\Application;

/**
 * Перечисление периодов для сбора и отображения статистики
 */
enum StatisticsPeriodEnum: string
{
    case TODAY = 'Сегодня';
    case WEEK = 'Неделя';
    case MONTH = 'Месяц';
    case TOTAL = 'Всего';
}

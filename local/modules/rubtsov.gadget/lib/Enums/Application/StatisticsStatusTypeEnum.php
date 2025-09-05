<?php

declare(strict_types=1);

namespace Rubtsov\Gadget\Enums\Application;

/**
 * Перечисление типов статусов заявок для статистики.
 */
enum StatisticsStatusTypeEnum: string
{
    case CLOSED = 'Закрытые';
    case OPEN = 'Не закрытые';
}

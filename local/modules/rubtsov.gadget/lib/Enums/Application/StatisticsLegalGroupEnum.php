<?php

declare(strict_types=1);

namespace Rubtsov\Gadget\Enums\Application;

/**
 * Перечисление групп заявителей по юридическому статусу
 */
enum StatisticsLegalGroupEnum: string
{
    case FL = 'Физ. партнёры';
    case UL = 'Юр. партнёры';
}

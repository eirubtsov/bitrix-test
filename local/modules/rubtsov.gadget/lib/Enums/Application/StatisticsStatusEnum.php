<?php

declare(strict_types=1);

namespace Rubtsov\Gadget\Enums\Application;

enum StatisticsStatusEnum: string
{
    case SUCCESS = 'Успешная';
    case WAIT = 'Ожидание';
    case SEND = 'Отправленная';
}

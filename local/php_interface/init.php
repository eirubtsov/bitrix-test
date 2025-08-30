<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

// Подключение composer
if (file_exists(realpath(__DIR__ . '/../') . '/vendor/autoload.php')) {
    require_once(realpath(__DIR__ . '/../') . '/vendor/autoload.php');
}

<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

// Подключение констант
if (file_exists(__DIR__ . '/include/constants.php')) {
    require_once(__DIR__ . '/include/constants.php');
}

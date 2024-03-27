<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

// Подключение автоподгрузки composer
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once(__DIR__ . '/vendor/autoload.php');
}

// Подключение автоподгрузки классов Rubtsov
if (file_exists(__DIR__ . '/include/autoloadRubtsovClasses.php')) {
    require_once(__DIR__ . '/include/autoloadRubtsovClasses.php');
}

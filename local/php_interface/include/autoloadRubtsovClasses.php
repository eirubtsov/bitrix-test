<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

spl_autoload_register(function ($className) {
    $baseName = 'Rubtsov';
    $className = trim(substr($className, strlen($baseName)), '\\');
    $classPath = $_SERVER['DOCUMENT_ROOT'] . '/local/Rubtsov/' . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
    if (file_exists($classPath)) {
        require_once($classPath);
    }
});

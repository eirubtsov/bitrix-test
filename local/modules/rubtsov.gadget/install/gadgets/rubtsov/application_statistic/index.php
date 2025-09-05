<?php

declare(strict_types=1);

global $APPLICATION;

$APPLICATION->IncludeComponent(
    'rubtsov:requests.gadget',
    '',
    [
        'CACHE_TIME' => '36000000',
        'CACHE_TYPE' => 'A',
    ],
    false,
);

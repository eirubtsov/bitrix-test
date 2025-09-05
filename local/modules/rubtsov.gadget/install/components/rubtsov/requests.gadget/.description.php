<?php

declare(strict_types=1);

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arComponentDescription = [
    'NAME' => Loc::getMessage('REQUEST_GADGET_DESC.NAME'),
    'DESCRIPTION' => Loc::getMessage('REQUEST_GADGET_DESC.DESCRIPTION'),
    'CACHE_PATH' => 'Y',
    'SORT' => 40,
    'PATH' => [
        'ID' => 'Local',
        'SORT' => 20,
    ],
];

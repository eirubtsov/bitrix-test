<?php

use \Rubtsov\Helper\IBlock;
use \Bitrix\Main\Type\DateTime;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

// Получение информации об инфоблоке "Участники"
$iBlock['PARTICIPANTS'] = IBlock::getInfoByCode(
    'participants',
    ['ID', 'IBLOCK_TYPE' => 'IBLOCK_TYPE_ID', 'API_CODE']
);

// Если в инфоблоке "Участники" зафиксирован API_CODE
if ($iBlock['PARTICIPANTS']['API_CODE']) {
    /** @var \Bitrix\Iblock\ElementTable $apiClassParticipants */

    // Генерация класса для работы с таблицей
    $apiClassParticipants = '\Bitrix\Iblock\Elements\Element' . $iBlock['PARTICIPANTS']['API_CODE'] . 'Table';

    // Выборка участников
    $participants = $apiClassParticipants::getList([
        'order' => ['NAME' => 'ASC'],
        'select' => [
            'ID',
            'NAME',
            'ACTIVE_FROM',
            'ACTIVE_TO',
            'EVENTS_ID' => 'EVENTS.ELEMENT.ID',
        ],
        'filter' => [
            'EVENTS.ELEMENT.ID' => $arResult['ELEMENTS'],
            'ACTIVE' => 'Y',
            [
                'LOGIC' => 'OR',
                '>=ACTIVE_TO' => new DateTime(),
                'ACTIVE_TO' => null,
            ],
            [
                'LOGIC' => 'OR',
                '<=ACTIVE_FROM' => new DateTime(),
                'ACTIVE_FROM' => null,
            ],
        ],
    ]);

    // Обработка полученных участников
    $arResult['EVENTS'] = [];
    while ($participant = $participants->fetch()) {
        $arResult['EVENTS'][$participant['EVENTS_ID']][$participant['ID']] = $participant;
    }
}

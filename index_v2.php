<?php

use \Rubtsov\Helper\IBlock;
use \Bitrix\Main\Type\DateTime;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

/** @global CMain $APPLICATION */
$APPLICATION->SetTitle('Тестовый раздел');

// Предположим, что город был определен ранее
$city = 'moscow';
?>

<?php
// Получение информации об инфоблоке "Мероприятия"
$iBlock['EVENTS'] = IBlock::getInfoByCode(
    'events',
    ['ID', 'IBLOCK_TYPE' => 'IBLOCK_TYPE_ID', 'API_CODE']
);

// Получение информации об инфоблоке "Участники"
$iBlock['PARTICIPANTS'] = IBlock::getInfoByCode(
    'participants',
    ['ID', 'IBLOCK_TYPE' => 'IBLOCK_TYPE_ID', 'API_CODE']
);

$data = [];
$ttl = 36000000;
$currentDateTime = new DateTime();

// Если в инфоблоке "Участники" зафиксирован API_CODE
if ($iBlock['EVENTS']['API_CODE']) {
    /** @var \Bitrix\Iblock\ElementTable $apiClassEvents */

    // Генерация класса для работы с таблицей
    $apiClassEvents = '\Bitrix\Iblock\Elements\Element' . $iBlock['EVENTS']['API_CODE'] . 'Table';

    // Выборка мероприятий
    $events = $apiClassEvents::getList([
        'order' => ['ACTIVE_FROM' => 'DESC'],
        'select' => [
            'ID',
            'NAME',
            'ACTIVE_FROM',
            'ACTIVE_TO',
        ],
        'filter' => [
            'CITY.ELEMENT.CODE' => $city,
            'ACTIVE' => 'Y',
            [
                'LOGIC' => 'OR',
                '>=ACTIVE_TO' => $currentDateTime,
                'ACTIVE_TO' => null,
            ],
            [
                'LOGIC' => 'OR',
                '<=ACTIVE_FROM' => $currentDateTime,
                'ACTIVE_FROM' => null,
            ],
        ],
        'cache' => [
            'ttl' => $ttl,
            'cache_joins' => true,
        ],
    ]);

    while ($event = $events->fetch()) {
        $data['EVENTS'][$event['ID']] = $event;
    }
}

if ($iBlock['PARTICIPANTS']['API_CODE'] && !empty($data['EVENTS'])) {
    /** @var \Bitrix\Iblock\ElementTable $apiClassParticipants */

    // Генерация класса для работы с таблицей
    $apiClassParticipants = '\Bitrix\Iblock\Elements\Element' . $iBlock['PARTICIPANTS']['API_CODE'] . 'Table';

    // Получение idшников мероприятий
    $eventsIds = array_column($data['EVENTS'], 'ID');

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
            'EVENTS.ELEMENT.ID' => $eventsIds,
            'ACTIVE' => 'Y',
            [
                'LOGIC' => 'OR',
                '>=ACTIVE_TO' => $currentDateTime,
                'ACTIVE_TO' => null,
            ],
            [
                'LOGIC' => 'OR',
                '<=ACTIVE_FROM' => $currentDateTime,
                'ACTIVE_FROM' => null,
            ],
        ],
        'cache' => [
            'ttl' => $ttl,
            'cache_joins' => true,
        ],
    ]);

    // Обработка полученных участников
    $data['PARTICIPANTS'] = [];
    while ($participant = $participants->fetch()) {
        $data['PARTICIPANTS'][$participant['EVENTS_ID']][$participant['ID']] = $participant;
    }
}
?>

<?php if ($data['EVENTS']): ?>
    <?php foreach ($data['EVENTS'] as $arEvent): ?>
        <h1><?= $arEvent['NAME'] ?></h1>
        <?php if ($data['PARTICIPANTS'][$arEvent['ID']]): ?>
            Список участников:
            <ul>
                <?php foreach ($data['PARTICIPANTS'][$arEvent['ID']] as $arParticipant): ?>
                    <li><?= $arParticipant['NAME'] ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            Участники еще не зарегистрированы
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>

<?php

namespace Rubtsov\Gadget\Helpers;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Loader;
use Exception;
use RuntimeException;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class PropertyHelper
{
    /**
     * Возвращает id свойства по его символьному коду и id инфоблока (результат кешируются)
     * @param int $iblockId
     * @param string $code
     * @return string|null
     */
    public static function getIdByCode(int $iblockId, string $code): ?string
    {
        try {
            if (!Loader::includeModule('iblock')) {
                throw new RuntimeException('Не установлен модуль "iblock"');
            }

            $res = PropertyTable::getList([
                'filter' => [
                    'IBLOCK_ID' => $iblockId,
                    'CODE' => $code,
                ],
                'select' => ['ID'],
                'limit' => 1,
                'cache' => [
                    'ttl' => 36000000,
                    'cache_joins' => true,
                ],
            ])->fetch();

            return $res['ID'] ?: null;
        } catch (Exception $exception) {
            return null;
        }
    }
}

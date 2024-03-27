<?php

namespace Rubtsov\Helper;

use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class IBlock
{
    /**
     * Получает информацию об инфоблоке по его символьному коду (результат кешируются)
     * @param string $iBlockCode
     * @return array
     */
    public static function getInfoByCode(string $iBlockCode, array $select = []): array
    {
        Loader::IncludeModule('iblock');

        if (count($select) == 0) {
            $select = ['IBLOCK_' => '*',];
        }

        $resIBlock = IblockTable::getList([
            'filter' => [
                'CODE' => $iBlockCode,
            ],
            'select' => $select,
            'limit' => 1,
            'cache' => [
                'ttl' => 36000000,
                'cache_joins' => true,
            ],
        ])->fetch();

        if ($resIBlock) {
            $res = $resIBlock;
        } else {
            $res['ERROR'] = 'Инфоблок не найден';
        }

        return $res;
    }

    /**
     * Получает информацию об инфоблоке по его ID (результат кешируются)
     * @param string $iBlockId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getInfoById(string $iBlockId, array $select = []): array
    {
        Loader::IncludeModule('iblock');

        if (count($select) == 0) {
            $select = ['IBLOCK_' => '*',];
        }

        $resIBlock = IblockTable::getList([
            'filter' => [
                'ID' => $iBlockId,
            ],
            'select' => $select,
            'limit' => 1,
            'cache' => [
                'ttl' => 36000000,
                'cache_joins' => true,
            ],
        ])->fetch();

        if ($resIBlock) {
            $res = $resIBlock;
        } else {
            $res['ERROR'] = 'Инфоблок не найден';
        }

        return $res;
    }

    /**
     * Получает все свойства инфоблока по его ID (результат кешируются)
     * @param string $iBlockID
     * @param int $ttl
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getPropertyById(string $iBlockID, int $ttl = 3600): array
    {
        Loader::IncludeModule('iblock');

        $rsProperty = PropertyTable::getList([
            'filter' => [
                'IBLOCK_ID' => $iBlockID,
                'ACTIVE' => 'Y',
            ],
            'cache' => [
                'ttl' => $ttl,
                'cache_joins' => true,
            ],
        ]);

        $res = [];
        while ($prop = $rsProperty->fetch()) {
            $res[$prop['CODE']] = $prop;
        }

        if ($res) {
            return $res;
        } else {
            return ['ERROR' => 'Свойства не найдены'];
        }
    }
}

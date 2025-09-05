<?php

declare(strict_types=1);

namespace Rubtsov\Gadget\Helpers;

use Bitrix\Iblock\Iblock;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\ORM\CommonElementTable;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Exception;
use RuntimeException;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class IBlockHelper
{
    private static bool $includeModule = false;

    /**
     * Проверка подключения модуля
     * @return void
     * @throws LoaderException
     */
    protected static function checkModule(): void
    {
        if (self::$includeModule) {
            return;
        }

        if (!Loader::includeModule('iblock')) {
            throw new RuntimeException('Не установлен модуль "iblock"');
        }

        self::$includeModule = true;
    }

    /**
     * Возвращает информацию об инфоблоке по его символьному коду (результат кешируются)
     * @param string $iBlockCode
     * @param array $select
     * @return array|null
     */
    public static function getInfoByCode(string $iBlockCode, array $select = []): ?array
    {
        try {
            static::checkModule();

            if (empty($select)) {
                $select = ['*'];
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

            return $resIBlock ?: null;
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Возвращает id инфоблока по его символьному коду (результат кешируются)
     * @param string $iBlockCode
     * @return int|null
     */
    public static function getIdByCode(string $iBlockCode): ?int
    {
        try {
            static::checkModule();

            $resIBlock = IblockTable::getList([
                'filter' => [
                    'CODE' => $iBlockCode,
                ],
                'select' => ['ID'],
                'limit' => 1,
                'cache' => [
                    'ttl' => 36000000,
                    'cache_joins' => true,
                ],
            ])->fetch();

            return (int)$resIBlock['ID'] ?: null;
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Возвращает класс динамически генерируемой ORM сущности по id ИБ
     * @param int $id
     * @return CommonElementTable|string|null
     */
    public static function getEntityById(int $id): CommonElementTable|string|null
    {
        try {
            static::checkModule();
            return Iblock::wakeUp($id)->getEntityDataClass();
        } catch (Exception $exception) {
            return null;
        }
    }
}

<?php

declare(strict_types=1);

namespace Weather\Forecast\Helpers;

use Bitrix\Main\Data\Cache;
use Closure;

class CacheHelper
{
    /**
     * Базовая директория кеша для модуля
     */
    protected const string BASE_PATH = '/' . WEATHER_FORECAST_MODULE_ID . '/' ;

    /**
     * Получить данные из кеша или записать их
     * @param string $key
     * @param int $ttl
     * @param Closure $callback
     * @param string $subPath
     * @return mixed
     */
    public static function remember(string $key, int $ttl, Closure $callback, string $subPath = ''): mixed
    {
        $path = static::BASE_PATH . $subPath;
        $cache = Cache::createInstance();

        if ($cache->initCache($ttl, $key, $path)) {
            return $cache->getVars();
        }

        if ($cache->startDataCache()) {
            $data = $callback();
            $cache->endDataCache($data);
            return $data;
        }

        return null;
    }

    /**
     * Получить из кеша (без генерации)
     * @param string $key
     * @param string $subPath
     * @return mixed
     */
    public static function get(string $key, string $subPath = ''): mixed
    {
        $path = static::BASE_PATH . $subPath;
        $cache = Cache::createInstance();

        if ($cache->initCache(PHP_INT_MAX, $key, $path)) {
            return $cache->getVars();
        }

        return null;
    }

    /**
     * Сохранить данные в кеш
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @param string $subPath
     * @return void+
     */
    public static function put(string $key, mixed $value, int $ttl, string $subPath = ''): void
    {
        $path = static::BASE_PATH . $subPath;
        $cache = Cache::createInstance();

        if ($cache->startDataCache($ttl, $key, $path)) {
            $cache->endDataCache($value);
        }
    }

    /**
     * Удалить кеш по ключу
     * @param string $key
     * @param string $subPath
     * @return void
     */
    public static function forget(string $key, string $subPath = ''): void
    {
        $path = static::BASE_PATH . $subPath;
        $cache = Cache::createInstance();
        $cache->clean($key, $path);
    }

    /**
     * Очистить весь кеш модуля
     * @return void
     */
    public static function flushAll(): void
    {
        $cache = Cache::createInstance();
        $cache->cleanDir(static::BASE_PATH);
    }
}

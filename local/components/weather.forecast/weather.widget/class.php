<?php

declare(strict_types=1);

use Bitrix\Main\Loader;
use Weather\Forecast\Facades\WeatherForecast;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class WeatherWidgetComponent extends CBitrixComponent
{
    /**
     * Обработка параметров
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams): array
    {
        $arParams['CACHE_TIME'] = $arParams['CACHE_TIME'] ?: 1800;
        $arParams['CITY'] = $arParams['CITY'] ?: 'Москва';

        return $arParams;
    }

    /**
     * @return void
     */
    public function executeComponent(): void
    {
        try {
            // Для формирования кеша в данном случае подходят все параметры по умолчанию
            if ($this->startResultCache()) {
                $this->arResult['WEATHER'] = $this->getWeatherData();

                // Фиксируем, что в кеше из arResult ничего не храним, только html
                // В данном случа незачем хранить лишние данные для component_epilog.php
                // В последствии можно добавить здесь или определить в result_modifier необходимые ключи
                $this->SetResultCacheKeys([]);
                $this->includeComponentTemplate();
            }
        } catch (Exception $exception) {
            ShowError($exception->getMessage());
        }
    }

    /**
     * Получение короткой сводки о погоде
     * @return array
     * @throws Exception
     */
    private function getWeatherData(): array
    {
        try {
            Loader::includeModule('weather.forecast');
            return WeatherForecast::getShortCurrentWeather(
                query: $this->arParams['CITY'] ?: '',
                units: $this->arParams['UNITS'] ?: ''
            )->toArray();
        } catch (Exception $exception) {
            // TODO: Добавить логирование
            throw new Exception('Не удалось получить данные о погоде');
        }
    }
}

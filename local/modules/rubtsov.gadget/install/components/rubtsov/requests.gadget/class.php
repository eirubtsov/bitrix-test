<?php

declare(strict_types=1);

use Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Main\Application;
use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\Web\Uri;
use Rubtsov\Gadget\Enums\Application\StatisticsLegalGroupEnum;
use Rubtsov\Gadget\Enums\Application\StatisticsPeriodEnum;
use Rubtsov\Gadget\Enums\Application\StatisticsStatusEnum;
use Rubtsov\Gadget\Enums\Application\StatisticsStatusTypeEnum;
use Rubtsov\Gadget\Factories\ApplicationPeriodStatisticBoundsFactory;
use Rubtsov\Gadget\Helpers\IBlockHelper;
use Rubtsov\Gadget\Helpers\PropertyHelper;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class RequestsGadgetComponent extends CBitrixComponent implements Errorable
{
    protected ErrorCollection $errorCollection;
    protected int $requestIblockId;
    protected string $requestIBlockCode = 'REQUESTS';

    /**
     * @param $component
     * @throws LoaderException
     */
    public function __construct($component = null)
    {
        parent::__construct($component);

        // Инициализация коллекции ошибок
        $this->errorCollection = new ErrorCollection();

        // Подключение необходимых модулей
        $includeModules = [
            'iblock',
            'rubtsov.gadget',
        ];
        foreach ($includeModules as $module) {
            if (!Loader::includeModule($module)) {
                $this->errorCollection[] = new Error("Не удалось подключить модуль {$module}");
            }
        }
    }

    /**
     * Обработка параметров
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams): array
    {
        $arParams['CACHE_TIME'] = (int)$arParams['CACHE_TIME'] ?: 36000000;
        return $arParams;
    }

    /**
     * @return void
     */
    public function executeComponent(): void
    {
        $app = Application::getInstance();
        $taggedCache = $app->getTaggedCache();

        try {
            // Проверка наличия ошибок на текущем этапе
            $this->checkForErrors();

            // Для связи управляемого кеша с кешем компонента, оба кеша должны находиться в одной директории
            if ($this->startResultCache(false, false, __CLASS__)) {
                $this->arResult['IBLOCK'] = $this->getRequestIblockInfo();
                $this->requestIblockId = (int)$this->arResult['IBLOCK']['ID'];

                if (defined('BX_COMP_MANAGED_CACHE')) {
                    $taggedCache->startTagCache(__CLASS__);
                    $taggedCache->registerTag("iblock_id_{$this->requestIblockId}");
                }

                // Получаем грид
                $this->arResult['GRID_PARAMS'] = $this->getGrid();

                // Подключение шаблона
                $this->includeComponentTemplate();

                // Если включен управляемы кеш
                if (defined('BX_COMP_MANAGED_CACHE')) {
                    $taggedCache->endTagCache();
                }
            }
        } catch (Exception $exception) {
            $this->AbortResultCache();
            if (defined('BX_COMP_MANAGED_CACHE')) {
                $taggedCache->endTagCache();
            }
            ShowError($exception->getMessage());
        }
    }

    /**
     * Получение информации по инфоблоку "Заявки на вывод"
     * @return array
     * @throws Exception
     */
    protected function getRequestIblockInfo(): array
    {
        /**
         * Так как это узконаправленная задача, символьный код инфоблока вшит в компонент
         */
        $res = IBlockHelper::getInfoByCode($this->requestIBlockCode, [
            'ID',
            'IBLOCK_TYPE_ID',
        ]);

        if (!empty($res)) {
            return $res;
        } else {
            throw new Exception('Не удалось получить информацию по инфоблоку "Заявки на вывод"');
        }
    }

    /**
     * Генерация данных для грида
     * @return array
     * @throws Exception
     */
    private function getGrid(): array
    {
        return [
            'GRID_ID' => __CLASS__,
            'COLUMNS' => [
                ['id' => 'METRIC', 'name' => '', 'default' => true],
                [
                    'id' => StatisticsPeriodEnum::TODAY->name,
                    'name' => StatisticsPeriodEnum::TODAY->value,
                    'default' => true,
                    'align' => 'center',
                ],
                [
                    'id' => StatisticsPeriodEnum::WEEK->name,
                    'name' => StatisticsPeriodEnum::WEEK->value,
                    'default' => true,
                    'align' => 'center',
                ],
                [
                    'id' => StatisticsPeriodEnum::MONTH->name,
                    'name' => StatisticsPeriodEnum::MONTH->value,
                    'default' => true,
                    'align' => 'center',
                ],
                [
                    'id' => StatisticsPeriodEnum::TOTAL->name,
                    'name' => StatisticsPeriodEnum::TOTAL->value,
                    'default' => true,
                    'align' => 'center',
                ],
            ],
            'ROWS' => $this->getGridRows(),

            // Убираем все лишние
            'SHOW_ROW_CHECKBOXES' => false,
            'SHOW_GRID_SETTINGS_MENU' => false,
            'SHOW_SELECTED_COUNTER' => false,
            'SHOW_TOTAL_COUNTER' => false,
            'SHOW_PAGINATION' => false,
            'SHOW_PAGESIZE' => false,
            'SHOW_ACTION_PANEL' => false,
            'ALLOW_COLUMNS_SORT' => false,
            'ALLOW_COLUMNS_RESIZE' => false,
            'ALLOW_HORIZONTAL_SCROLL' => false,
            'ALLOW_SORT' => false,
            'AJAX_MODE' => 'N',
            'ALLOW_PIN_HEADER' => false,
            'ENABLE_FIELDS_SEARCH' => false,
        ];
    }

    /**
     * Генерация строк грида
     * @return array[]
     * @throws Exception
     */
    protected function getGridRows(): array
    {
        $data = $this->getData();
        $rows = [];

        // Собираем в кучу все периоды для статистики
        $periods = array_map(
            static fn(StatisticsPeriodEnum $case) => $case->name,
            StatisticsPeriodEnum::cases()
        );

        // Перебираем все типы статусов
        foreach ($data as $groupStatusKey => $groupStatus) {
            $rowData = [
                'id' => "GROUP_$groupStatusKey",
            ];

            $groupStatusEnum = StatisticsStatusTypeEnum::{$groupStatusKey};
            // Если не удалось определить тип статуса заявок
            if (empty($groupStatusEnum)) {
                continue;
            }

            // Получаем ссылку на страницу инфоблока с предустановленным фильтром
            $rowData['columns']['METRIC'] = $this->getLinkToFilter($groupStatusEnum);
            $rows[] = $rowData;

            // Перебираем все типы заявителей
            foreach ($groupStatus as $groupLegalKey => $groupLegal) {
                $rowData = [
                    'id' => "{$groupStatusKey}_$groupLegalKey",
                ];

                // Перебираем зарегистрированные периоды
                foreach ($periods as $period) {
                    $periodEnum = StatisticsPeriodEnum::{$period};
                    // Если не удалось определить период
                    if (empty($periodEnum)) {
                        continue;
                    }

                    // Фиксируем значение по периоду
                    $rowData['columns'][$period] = $groupLegal['CNT_' . $periodEnum->name] ?: 0;
                }

                // Получаем название группы
                $legalGroupEnum = StatisticsLegalGroupEnum::{$groupLegal['LEGAL_GROUP']};
                if (!empty($legalGroupEnum)) {
                    $rowData['columns']['METRIC'] = "- {$legalGroupEnum->value}";
                }

                $rows[] = $rowData;
            }
        }

        return $rows;
    }

    /**
     * Формирование ссылки на страницу инфоблока с предустановленным фильтром
     * @param StatisticsStatusTypeEnum $type
     * @return string
     */
    protected function getLinkToFilter(StatisticsStatusTypeEnum $type): string
    {
        // Формируем сопоставление элементов списка с зарегистрированными
        $enumMap = $this->getStatusesMap();
        $statusPropertyId = PropertyHelper::getIdByCode($this->requestIblockId, 'STATUS');

        // Если не удалось сформировать карту или получить ID свойства, то просто отдаем значение без ссылки
        if (empty($enumMap) || empty($statusPropertyId)) {
            return $type->value;
        }

        // Подготавливаем основу для ссылки
        $uri = new Uri('/bitrix/admin/iblock_list_admin.php');
        $uri->addParams([
            'IBLOCK_ID' => $this->arResult['IBLOCK']['ID'],
            'type' => $this->arResult['IBLOCK']['IBLOCK_TYPE_ID'],
            'lang' => LANGUAGE_ID,
            'apply_filter' => 'Y',
        ]);

        // Добавляем фильтры в зависимости от статуса заявки
        switch ($type) {
            case StatisticsStatusTypeEnum::CLOSED:
                $uri->addParams([
                    "PROPERTY_{$statusPropertyId}" => [$enumMap[StatisticsStatusEnum::SUCCESS->name]],
                ]);
                break;

            case StatisticsStatusTypeEnum::OPEN:
                $uri->addParams([
                    "PROPERTY_{$statusPropertyId}" => [
                        $enumMap[StatisticsStatusEnum::WAIT->name],
                        $enumMap[StatisticsStatusEnum::SEND->name],
                    ],
                ]);
                break;
        }

        return "<a href=\"{$uri->getUri()}\"><b>{$type->value}</b></a>";
    }

    /**
     * Формируем сопоставление статусов с их XML_ID
     * @return array|null
     */
    protected function getStatusesMap(): ?array
    {
        try {
            $res = PropertyEnumerationTable::getList([
                'select' => ['ID', 'XML_ID'],
                'filter' => [
                    '=PROPERTY.IBLOCK_ID' => $this->requestIblockId,
                    '=PROPERTY.CODE' => 'STATUS',
                ],
            ])->fetchAll();

            $map = [];
            foreach ($res as $row) {
                $map[$row['XML_ID']] = (int)$row['ID'];
            }

            return $map ?: null;
        } catch (Exception $exception) {
            // TODO: Добавить логирование
            return null;
        }
    }

    /**
     * Запрашиваем статистику
     * @return array
     * @throws Exception
     */
    protected function getData(): array
    {
        try {
            // Получаем сущность инфоблока с заявками
            $dataClass = IBlockHelper::getEntityById($this->requestIblockId);
            // Начинаем формировать запрос
            $query = new Query($dataClass);

            // Поля, которые используем для построения запроса
            $statusField = 'STATUS.ITEM.XML_ID';
            $legalField = 'STAT_IS_LEGAL.VALUE';
            $dateCol = 'ACTIVE_FROM';

            // Формируем периоды для сбора статистики
            $periodsBounds = ApplicationPeriodStatisticBoundsFactory::make();

            // Преобразуем границы в SQL-литералы, совместимые с текущей СУБД.
            $helper = Application::getConnection()->getSqlHelper();

            // Лучше задавать время явно, чтобы включить обе границы интервала.
            $todayFromSql = $helper->convertToDbDateTime($periodsBounds->today->from);
            $todayToSql = $helper->convertToDbDateTime($periodsBounds->today->to);

            $weekFromSql = $helper->convertToDbDateTime($periodsBounds->week->from);
            $weekToSql = $helper->convertToDbDateTime($periodsBounds->week->to);

            $monthFromSql = $helper->convertToDbDateTime($periodsBounds->month->from);
            $monthToSql = $helper->convertToDbDateTime($periodsBounds->month->to);

            // Группа статуса: CLOSED = SUCCESS; OPEN = WAIT|SEND.
            $query->registerRuntimeField(
                new ExpressionField(
                    'STATUS_GROUP',
                    "CASE 
                        WHEN UPPER(%s) = '" . StatisticsStatusEnum::SUCCESS->name . "' THEN '" . StatisticsStatusTypeEnum::CLOSED->name . "'
                        WHEN UPPER(%s) IN ('" . StatisticsStatusEnum::WAIT->name . "','" . StatisticsStatusEnum::SEND->name . "') THEN '" . StatisticsStatusTypeEnum::OPEN->name . "'
                        ELSE 'OPEN'
                    END",
                    [$statusField, $statusField]
                )
            );
            // Определение юридического статуса заявителя
            $query->registerRuntimeField(
                new ExpressionField(
                    'LEGAL_GROUP',
                    "CASE WHEN %s IS NULL OR %s = '' THEN '"
                    . StatisticsLegalGroupEnum::FL->name
                    . "' ELSE '"
                    . StatisticsLegalGroupEnum::UL->name
                    . "' END",
                    [$legalField, $legalField]
                )
            );
            // Подсчет общего кол-ва заявок
            $query->registerRuntimeField(
                new ExpressionField('CNT_' . StatisticsPeriodEnum::TOTAL->name, 'COUNT(1)')
            );
            // Подсчет заявок за "Сегодня"
            $query->registerRuntimeField(
                new ExpressionField(
                    'CNT_' . StatisticsPeriodEnum::TODAY->name,
                    "SUM(CASE WHEN %s BETWEEN {$todayFromSql} AND {$todayToSql} THEN 1 ELSE 0 END)",
                    $dateCol
                )
            );
            // Подсчет заявок за "Неделю"
            $query->registerRuntimeField(
                new ExpressionField(
                    "CNT_" . StatisticsPeriodEnum::WEEK->name,
                    "SUM(CASE WHEN %s BETWEEN {$weekFromSql} AND {$weekToSql} THEN 1 ELSE 0 END)",
                    $dateCol
                )
            );
            // Подсчет заявок за "Месяц"
            $query->registerRuntimeField(
                new ExpressionField(
                    'CNT_' . StatisticsPeriodEnum::MONTH->name,
                    "SUM(CASE WHEN %s BETWEEN {$monthFromSql} AND {$monthToSql} THEN 1 ELSE 0 END)",
                    $dateCol
                )
            );

            $query->setSelect([
                'STATUS_GROUP',
                'LEGAL_GROUP',
                'CNT_' . StatisticsPeriodEnum::TOTAL->name,
                'CNT_' . StatisticsPeriodEnum::TODAY->name,
                'CNT_' . StatisticsPeriodEnum::WEEK->name,
                'CNT_' . StatisticsPeriodEnum::MONTH->name,
            ]);

            // Статусы заявок по которым считаем статистику
            $statuses = array_map(
                static fn(StatisticsStatusEnum $case) => $case->name,
                StatisticsStatusEnum::cases()
            );
            $query->setFilter([
                '=IBLOCK_ID' => $this->requestIblockId,
                "@{$statusField}" => $statuses,
                '=ACTIVE' => 'Y',
            ]);
            $query->setGroup(['STATUS_GROUP', 'LEGAL_GROUP']);;

            $res = $query->exec()->fetchAll();

            if (empty($res)) {
                throw new Exception('Не удалось собрать статистику по заявкам');
            }

            // Фиксация структуры для единообразной сортировки строк
            $data = [
                StatisticsStatusTypeEnum::CLOSED->name => [
                    StatisticsLegalGroupEnum::FL->name => [],
                    StatisticsLegalGroupEnum::UL->name => [],
                ],
                StatisticsStatusTypeEnum::OPEN->name => [
                    StatisticsLegalGroupEnum::FL->name => [],
                    StatisticsLegalGroupEnum::UL->name => [],
                ],
            ];
            foreach ($res as $item) {
                $data[$item['STATUS_GROUP']][$item['LEGAL_GROUP']] = $item;
            }

            return $data;
        } catch (Exception $e) {
            // TODO: Добавить логирование
            throw new Exception('Не удалось собрать статистику по заявкам');
        }
    }

    /**
     * Проверка наличия ошибок (при наличии ошибок выбрасывает исключение)
     * @return void
     * @throws Exception
     */
    private function checkForErrors(): void
    {
        if (!empty($this->getErrors())) {
            foreach ($this->getErrors() as $error) {
                throw new Exception($error->getMessage(), $error->getCode());
            }
        }
    }

    /**
     * @return array|Error[]
     */
    public function getErrors(): array
    {
        return $this->errorCollection->toArray();
    }

    /**
     * @param $code
     * @return Error|null
     */
    public function getErrorByCode($code): ?Error
    {
        return $this->errorCollection->getErrorByCode($code);
    }
}

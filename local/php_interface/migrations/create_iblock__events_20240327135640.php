<?php

namespace Sprint\Migration;


class create_iblock__events_20240327135640 extends Version
{
    protected $description = "Создание инфоблока \"Мероприятия\"";

    protected $moduleVersion = "4.6.1";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->saveIblock([
            'IBLOCK_TYPE_ID' => 'content',
            'LID' =>
                [
                    0 => 's1',
                ],
            'CODE' => 'events',
            'API_CODE' => 'Events',
            'REST_ON' => 'N',
            'NAME' => 'Мероприятия',
            'ACTIVE' => 'Y',
            'SORT' => '200',
            'LIST_PAGE_URL' => '',
            'DETAIL_PAGE_URL' => '',
            'SECTION_PAGE_URL' => '',
            'CANONICAL_PAGE_URL' => '',
            'PICTURE' => null,
            'DESCRIPTION' => '',
            'DESCRIPTION_TYPE' => 'text',
            'RSS_TTL' => '24',
            'RSS_ACTIVE' => 'Y',
            'RSS_FILE_ACTIVE' => 'N',
            'RSS_FILE_LIMIT' => null,
            'RSS_FILE_DAYS' => null,
            'RSS_YANDEX_ACTIVE' => 'N',
            'XML_ID' => null,
            'INDEX_ELEMENT' => 'N',
            'INDEX_SECTION' => 'N',
            'WORKFLOW' => 'N',
            'BIZPROC' => 'N',
            'SECTION_CHOOSER' => 'L',
            'LIST_MODE' => '',
            'RIGHTS_MODE' => 'S',
            'SECTION_PROPERTY' => 'N',
            'PROPERTY_INDEX' => 'N',
            'VERSION' => '2',
            'LAST_CONV_ELEMENT' => '0',
            'SOCNET_GROUP_ID' => null,
            'EDIT_FILE_BEFORE' => '',
            'EDIT_FILE_AFTER' => '',
            'SECTIONS_NAME' => 'Разделы',
            'SECTION_NAME' => 'Раздел',
            'ELEMENTS_NAME' => 'Мероприятия',
            'ELEMENT_NAME' => 'Мероприятие',
            'EXTERNAL_ID' => null,
            'LANG_DIR' => '/',
            'IPROPERTY_TEMPLATES' =>
                [
                ],
            'ELEMENT_ADD' => 'Добавить мероприятие',
            'ELEMENT_EDIT' => 'Изменить мероприятие',
            'ELEMENT_DELETE' => 'Удалить мероприятие',
            'SECTION_ADD' => 'Добавить раздел',
            'SECTION_EDIT' => 'Изменить раздел',
            'SECTION_DELETE' => 'Удалить раздел',
        ]);
        $helper->Iblock()->saveIblockFields($iblockId, [
            'IBLOCK_SECTION' =>
                [
                    'NAME' => 'Привязка к разделам',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        [
                            'KEEP_IBLOCK_SECTION_ID' => 'N',
                        ],
                    'VISIBLE' => 'Y',
                ],
            'ACTIVE' =>
                [
                    'NAME' => 'Активность',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => 'Y',
                    'VISIBLE' => 'Y',
                ],
            'ACTIVE_FROM' =>
                [
                    'NAME' => 'Начало активности',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => '=now',
                    'VISIBLE' => 'Y',
                ],
            'ACTIVE_TO' =>
                [
                    'NAME' => 'Окончание активности',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                    'VISIBLE' => 'Y',
                ],
            'SORT' =>
                [
                    'NAME' => 'Сортировка',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '500',
                    'VISIBLE' => 'Y',
                ],
            'NAME' =>
                [
                    'NAME' => 'Название',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => '',
                    'VISIBLE' => 'Y',
                ],
            'PREVIEW_PICTURE' =>
                [
                    'NAME' => 'Картинка для анонса',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        [
                            'FROM_DETAIL' => 'N',
                            'UPDATE_WITH_DETAIL' => 'N',
                            'DELETE_WITH_DETAIL' => 'N',
                            'SCALE' => 'N',
                            'WIDTH' => '',
                            'HEIGHT' => '',
                            'IGNORE_ERRORS' => 'N',
                            'METHOD' => 'resample',
                            'COMPRESSION' => 95,
                            'USE_WATERMARK_TEXT' => 'N',
                            'WATERMARK_TEXT' => '',
                            'WATERMARK_TEXT_FONT' => '',
                            'WATERMARK_TEXT_COLOR' => '',
                            'WATERMARK_TEXT_SIZE' => '',
                            'WATERMARK_TEXT_POSITION' => 'tl',
                            'USE_WATERMARK_FILE' => 'N',
                            'WATERMARK_FILE' => '',
                            'WATERMARK_FILE_ALPHA' => '',
                            'WATERMARK_FILE_POSITION' => 'tl',
                            'WATERMARK_FILE_ORDER' => '',
                        ],
                    'VISIBLE' => 'Y',
                ],
            'PREVIEW_TEXT_TYPE' =>
                [
                    'NAME' => 'Тип описания для анонса',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => 'text',
                    'VISIBLE' => 'Y',
                ],
            'PREVIEW_TEXT' =>
                [
                    'NAME' => 'Описание для анонса',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                    'VISIBLE' => 'Y',
                ],
            'DETAIL_PICTURE' =>
                [
                    'NAME' => 'Детальная картинка',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        [
                            'SCALE' => 'N',
                            'WIDTH' => '',
                            'HEIGHT' => '',
                            'IGNORE_ERRORS' => 'N',
                            'METHOD' => 'resample',
                            'COMPRESSION' => 95,
                            'USE_WATERMARK_TEXT' => 'N',
                            'WATERMARK_TEXT' => '',
                            'WATERMARK_TEXT_FONT' => '',
                            'WATERMARK_TEXT_COLOR' => '',
                            'WATERMARK_TEXT_SIZE' => '',
                            'WATERMARK_TEXT_POSITION' => 'tl',
                            'USE_WATERMARK_FILE' => 'N',
                            'WATERMARK_FILE' => '',
                            'WATERMARK_FILE_ALPHA' => '',
                            'WATERMARK_FILE_POSITION' => 'tl',
                            'WATERMARK_FILE_ORDER' => '',
                        ],
                    'VISIBLE' => 'Y',
                ],
            'DETAIL_TEXT_TYPE' =>
                [
                    'NAME' => 'Тип детального описания',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => 'text',
                    'VISIBLE' => 'Y',
                ],
            'DETAIL_TEXT' =>
                [
                    'NAME' => 'Детальное описание',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                    'VISIBLE' => 'Y',
                ],
            'XML_ID' =>
                [
                    'NAME' => 'Внешний код',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => '',
                    'VISIBLE' => 'Y',
                ],
            'CODE' =>
                [
                    'NAME' => 'Символьный код',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' =>
                        [
                            'UNIQUE' => 'Y',
                            'TRANSLITERATION' => 'Y',
                            'TRANS_LEN' => 100,
                            'TRANS_CASE' => 'L',
                            'TRANS_SPACE' => '-',
                            'TRANS_OTHER' => '-',
                            'TRANS_EAT' => 'Y',
                            'USE_GOOGLE' => 'N',
                        ],
                    'VISIBLE' => 'Y',
                ],
            'TAGS' =>
                [
                    'NAME' => 'Теги',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                    'VISIBLE' => 'Y',
                ],
            'SECTION_NAME' =>
                [
                    'NAME' => 'Название',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => '',
                    'VISIBLE' => 'Y',
                ],
            'SECTION_PICTURE' =>
                [
                    'NAME' => 'Картинка для анонса',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        [
                            'FROM_DETAIL' => 'N',
                            'UPDATE_WITH_DETAIL' => 'N',
                            'DELETE_WITH_DETAIL' => 'N',
                            'SCALE' => 'N',
                            'WIDTH' => '',
                            'HEIGHT' => '',
                            'IGNORE_ERRORS' => 'N',
                            'METHOD' => 'resample',
                            'COMPRESSION' => 95,
                            'USE_WATERMARK_TEXT' => 'N',
                            'WATERMARK_TEXT' => '',
                            'WATERMARK_TEXT_FONT' => '',
                            'WATERMARK_TEXT_COLOR' => '',
                            'WATERMARK_TEXT_SIZE' => '',
                            'WATERMARK_TEXT_POSITION' => 'tl',
                            'USE_WATERMARK_FILE' => 'N',
                            'WATERMARK_FILE' => '',
                            'WATERMARK_FILE_ALPHA' => '',
                            'WATERMARK_FILE_POSITION' => 'tl',
                            'WATERMARK_FILE_ORDER' => '',
                        ],
                    'VISIBLE' => 'Y',
                ],
            'SECTION_DESCRIPTION_TYPE' =>
                [
                    'NAME' => 'Тип описания',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => 'text',
                    'VISIBLE' => 'Y',
                ],
            'SECTION_DESCRIPTION' =>
                [
                    'NAME' => 'Описание',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                    'VISIBLE' => 'Y',
                ],
            'SECTION_DETAIL_PICTURE' =>
                [
                    'NAME' => 'Детальная картинка',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        [
                            'SCALE' => 'N',
                            'WIDTH' => '',
                            'HEIGHT' => '',
                            'IGNORE_ERRORS' => 'N',
                            'METHOD' => 'resample',
                            'COMPRESSION' => 95,
                            'USE_WATERMARK_TEXT' => 'N',
                            'WATERMARK_TEXT' => '',
                            'WATERMARK_TEXT_FONT' => '',
                            'WATERMARK_TEXT_COLOR' => '',
                            'WATERMARK_TEXT_SIZE' => '',
                            'WATERMARK_TEXT_POSITION' => 'tl',
                            'USE_WATERMARK_FILE' => 'N',
                            'WATERMARK_FILE' => '',
                            'WATERMARK_FILE_ALPHA' => '',
                            'WATERMARK_FILE_POSITION' => 'tl',
                            'WATERMARK_FILE_ORDER' => '',
                        ],
                    'VISIBLE' => 'Y',
                ],
            'SECTION_XML_ID' =>
                [
                    'NAME' => 'Внешний код',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                    'VISIBLE' => 'Y',
                ],
            'SECTION_CODE' =>
                [
                    'NAME' => 'Символьный код',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        [
                            'UNIQUE' => 'N',
                            'TRANSLITERATION' => 'N',
                            'TRANS_LEN' => 100,
                            'TRANS_CASE' => 'L',
                            'TRANS_SPACE' => '-',
                            'TRANS_OTHER' => '-',
                            'TRANS_EAT' => 'Y',
                            'USE_GOOGLE' => 'N',
                        ],
                    'VISIBLE' => 'Y',
                ],
            'LOG_SECTION_ADD' =>
                [
                    'NAME' => 'LOG_SECTION_ADD',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => null,
                    'VISIBLE' => 'Y',
                ],
            'LOG_SECTION_EDIT' =>
                [
                    'NAME' => 'LOG_SECTION_EDIT',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => null,
                    'VISIBLE' => 'Y',
                ],
            'LOG_SECTION_DELETE' =>
                [
                    'NAME' => 'LOG_SECTION_DELETE',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => null,
                    'VISIBLE' => 'Y',
                ],
            'LOG_ELEMENT_ADD' =>
                [
                    'NAME' => 'LOG_ELEMENT_ADD',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => null,
                    'VISIBLE' => 'Y',
                ],
            'LOG_ELEMENT_EDIT' =>
                [
                    'NAME' => 'LOG_ELEMENT_EDIT',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => null,
                    'VISIBLE' => 'Y',
                ],
            'LOG_ELEMENT_DELETE' =>
                [
                    'NAME' => 'LOG_ELEMENT_DELETE',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => null,
                    'VISIBLE' => 'Y',
                ],
        ]);
        $helper->Iblock()->saveGroupPermissions($iblockId, [
            'administrators' => 'X',
            'everyone' => 'R',
        ]);
        $helper->Iblock()->saveProperty($iblockId, [
            'NAME' => 'Город',
            'ACTIVE' => 'Y',
            'SORT' => '100',
            'CODE' => 'CITY',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'E',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => null,
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => 'content:cities',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'Y',
            'VERSION' => '2',
            'USER_TYPE' => null,
            'USER_TYPE_SETTINGS' => 'a:0:{}',
            'HINT' => '',
            'FEATURES' =>
                [
                    0 =>
                        [
                            'MODULE_ID' => 'iblock',
                            'FEATURE_ID' => 'DETAIL_PAGE_SHOW',
                            'IS_ENABLED' => 'N',
                        ],
                    1 =>
                        [
                            'MODULE_ID' => 'iblock',
                            'FEATURE_ID' => 'LIST_PAGE_SHOW',
                            'IS_ENABLED' => 'N',
                        ],
                ],
        ]);
        $helper->UserOptions()->saveElementForm($iblockId, [
            'Параметры|edit1' =>
                [
                    'ID' => 'ID',
                    'DATE_CREATE' => 'Создан',
                    'TIMESTAMP_X' => 'Изменен',
                    'ACTIVE' => 'Активность',
                    'ACTIVE_FROM' => 'Начало активности',
                    'ACTIVE_TO' => 'Окончание активности',
                    'NAME' => 'Название',
                    'CODE' => 'Символьный код',
                    'IBLOCK_ELEMENT_PROP_VALUE' => 'Значения свойств',
                    'PROPERTY_CITY' => 'Город',
                ],
        ]);
        $helper->UserOptions()->saveElementGrid($iblockId, [
            'views' =>
                [
                    'default' =>
                        [
                            'columns' =>
                                [
                                    0 => '',
                                ],
                            'columns_sizes' =>
                                [
                                    'expand' => 1,
                                    'columns' =>
                                        [
                                        ],
                                ],
                            'sticked_columns' =>
                                [
                                ],
                            'custom_names' =>
                                [
                                ],
                        ],
                ],
            'filters' =>
                [
                ],
            'current_view' => 'default',
        ]);
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $helper->Iblock()->deleteIblockIfExists('events');
    }
}

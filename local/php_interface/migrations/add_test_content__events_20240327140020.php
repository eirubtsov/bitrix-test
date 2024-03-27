<?php

namespace Sprint\Migration;

class add_test_content__events_20240327140020 extends Version
{
    protected $description = "Добавление тестового контента в инфоблок \"Мероприятия\"";
    protected $moduleVersion = "4.6.1";

    /**
     * @return bool|void
     * @throws Exceptions\RestartException
     * @throws Exceptions\MigrationException
     */
    public function up()
    {
        $this->getExchangeManager()
            ->IblockElementsImport()
            ->setExchangeResource('iblock_elements.xml')
            ->setLimit(20)
            ->execute(function ($item) {
                $this->getHelperManager()
                    ->Iblock()
                    ->addElement(
                        $item['iblock_id'],
                        $item['fields'],
                        $item['properties']
                    );
            });
    }

    /**
     * @return bool|void
     * @throws Exceptions\RestartException
     * @throws Exceptions\MigrationException
     */
    public function down()
    {
    }
}

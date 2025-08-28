<?php

declare(strict_types=1);

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\ModuleManager;

class weather_forecast extends CModule
{
    /** @var string $MODULE_ID */
    public $MODULE_ID = 'weather.forecast';

    /** @var string $MODULE_VERSION */
    public $MODULE_VERSION;

    /** @var string $MODULE_VERSION_DATE */
    public $MODULE_VERSION_DATE;

    /** @var string $MODULE_NAME */
    public $MODULE_NAME;

    /** @var string $MODULE_DESCRIPTION */
    public $MODULE_DESCRIPTION;

    /** @var string $PARTNER_NAME */
    public $PARTNER_NAME;

    /** @var string $PARTNER_URI */
    public $PARTNER_URI;

    /** @var string $MODULE_PATH */
    public string $MODULE_PATH;

    function __construct()
    {
        $this->MODULE_NAME = 'API Прогноза погоды';
        $this->PARTNER_NAME = 'Local';
        $this->PARTNER_URI = 'https://bitrix.my-email.ru/';

        $arModuleVersion = [];
        $this->MODULE_PATH = $this->getModulePath();
        include $this->MODULE_PATH . '/install/version.php';

        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }

    /**
     * Возвращает путь до модуля
     * @return string
     */
    protected function getModulePath(): string
    {
        $modulePath = explode('/', __FILE__);
        $modulePath = array_slice($modulePath, 0, array_search($this->MODULE_ID, $modulePath) + 1);

        return join('/', $modulePath);
    }

    /**
     * @return void
     */
    function doInstall(): void
    {
        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * @return void
     * @throws ArgumentNullException
     */
    function doUninstall(): void
    {
        $this->unInstallDB();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @return void
     * @throws ArgumentNullException
     */
    function unInstallDB(): void
    {
        Option::delete($this->MODULE_ID);
    }
}

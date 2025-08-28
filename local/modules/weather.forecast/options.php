<?php

declare (strict_types = 1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $APPLICATION;

use Bitrix\Main\AccessDeniedException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Context;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;

IncludeModuleLangFile(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();
$moduleId = $module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);
$request = Context::getCurrent()->getRequest();

try {
    Loader::includeModule($moduleId);
} catch (LoaderException $e) {
    CAdminMessage::ShowMessage($e->getMessage());
}

/**
 * Проверка прав доступа
 */
$rights = $APPLICATION->GetGroupRight($moduleId);
if ($rights < 'R') {
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}

/**
 * Категории настроек (tabs)
 */
$aTabs = [
    [
        'DIV' => 'main',
        'TAB' => 'Настройки API',
        'ICON' => '',
        'TITLE' => 'Настройки API',
        'OPTIONS' => [
            [
                'api_base_url',
                'Базовый url API',
                'https://api.openweathermap.org',
                ['text', 50],
            ],
            [
                'api_key',
                'Ключ API',
                '',
                ['password', 50],
            ],
        ],
    ],
];

/**
 * Сохранение параметров
 */
if ($request->isPost() && ($request->getPost('save') || $request->getPost('apply')) && check_bitrix_sessid()) {
    try {
        if ($rights < "W") {
            throw new AccessDeniedException();
        }
        if (!check_bitrix_sessid()) {
            throw new ArgumentException('Bad sessid.');
        }

        foreach ($aTabs as $aTab) {
            __AdmSettingsSaveOptions($moduleId, $aTab['OPTIONS']);
        }
        LocalRedirect(
            $APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID .
            '&mid_menu=1&mid=' . urlencode($moduleId) . '&tabControl_active_tab=' .
            urlencode($_REQUEST['tabControl_active_tab']) . '&sid=' .
            urlencode(SITE_ID)
        );
    } catch (Exception $exception) {
        CAdminMessage::ShowMessage($exception->getMessage());
    }
}

/**
 * Создание формы
 */
$tabControl = new CAdminTabControl('tabControl', $aTabs);
?>
<form method="POST"
      enctype="multipart/form-data"
      action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($moduleId) ?>&lang=<?= LANGUAGE_ID ?>">
    <?php
    $tabControl->Begin();
    foreach ($aTabs as $aTab) {
        $tabControl->BeginNextTab();
        if (!empty($aTab['OPTIONS'])) {
            __AdmSettingsDrawList($moduleId, $aTab['OPTIONS']);
        } elseif ($aTab['DIV'] === 'access_rights') {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php');
        }
    }

    $tabControl->buttons();
    ?>
    <input type="submit" name="apply"
           value="Применить" class="adm-btn-save"/>
    <?php
    echo bitrix_sessid_post();
    $tabControl->end();
    ?>
</form>

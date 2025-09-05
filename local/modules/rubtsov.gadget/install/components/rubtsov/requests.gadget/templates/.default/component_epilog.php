<?php

declare(strict_types=1);

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>

<?php if ($arResult['GRID_PARAMS']): ?>
    <div id="request-gadget__grid">
        <?php
        $APPLICATION->IncludeComponent(
                'bitrix:main.ui.grid',
                '',
                $arResult['GRID_PARAMS']
        );
        ?>
    </div>
<?php else: ?>
    <?php ShowError('Не удалось получить данные для отображения статистики'); ?>
<?php endif; ?>

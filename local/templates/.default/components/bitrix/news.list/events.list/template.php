<?php

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
$this->setFrameMode(true);
?>
<?php if ($arResult['ITEMS']): ?>
    <?php foreach ($arResult['ITEMS'] as $arItem): ?>
        <?php
        $this->AddEditAction(
            $arItem['ID'],
            $arItem['EDIT_LINK'],
            CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT')
        );
        $this->AddDeleteAction(
            $arItem['ID'],
            $arItem['DELETE_LINK'],
            CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'),
            ['CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')]
        );
        ?>
        <h1 id="<?= $this->GetEditAreaId($arItem['ID']); ?>"><?= $arItem['NAME'] ?></h1>
        <?php if ($arResult['EVENTS'][$arItem['ID']]): ?>
            <?= GetMessage('RUBTSOV_NEWS_LIST_EVENTS_LIST_NAME_LIST'); ?>
            <ul>
                <?php foreach ($arResult['EVENTS'][$arItem['ID']] as $arParticipant): ?>
                    <li><?= $arParticipant['NAME'] ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?= GetMessage('RUBTSOV_NEWS_LIST_EVENTS_LIST_NAME_LIST_NOT_ELEMENTS'); ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($arParams['DISPLAY_BOTTOM_PAGER']): ?>
        <br/><?= $arResult['NAV_STRING'] ?>
    <?php endif; ?>
<?php endif; ?>

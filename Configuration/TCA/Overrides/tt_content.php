<?php
defined('TYPO3_MODE') || die;

call_user_func(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'pxa_product_manager',
        'Pi1',
        'Product Manager'
    );

    $pluginSignature = 'pxaproductmanager_pi1';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:pxa_product_manager/Configuration/FlexForms/flexform_pi1.xml'
    );
});

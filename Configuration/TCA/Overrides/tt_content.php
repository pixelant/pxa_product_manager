<?php

defined('TYPO3_MODE') || die;

(function (): void {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'pxa_product_manager',
        'ProductList',
        'Product List',
        'EXT:pxa_product_manager/Resources/Public/Icons/Svg/Extension.svg',
        'Product Manager'
    );

    $pluginSignature = 'pxaproductmanager_productlist';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:pxa_product_manager/Configuration/FlexForms/flexform_product_list.xml'
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'pxa_product_manager',
        'ProductShow',
        'Product Show',
        'EXT:pxa_product_manager/Resources/Public/Icons/Svg/Extension.svg',
        'Product Manager'
    );

    $pluginSignature = 'pxaproductmanager_productshow';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:pxa_product_manager/Configuration/FlexForms/flexform_product_show.xml'
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'pxa_product_manager',
        'ProductRender',
        'Product Render',
        'EXT:pxa_product_manager/Resources/Public/Icons/Svg/Extension.svg',
        'Product Manager'
    );

    $pluginSignature = 'pxaproductmanager_pi1';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:pxa_product_manager/Configuration/FlexForms/flexform_pi1.xml'
    );
})();

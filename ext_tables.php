<?php
defined('TYPO3_MODE') || die;

(function () {
    // Allow tables on standard pages
    $tablesOnStandardPages = [
        'tx_pxaproductmanager_domain_model_product',
        'tx_pxaproductmanager_domain_model_attribute',
        'tx_pxaproductmanager_domain_model_attributeset',
        'tx_pxaproductmanager_domain_model_attributevalue',
        'tx_pxaproductmanager_domain_model_option',
        'tx_pxaproductmanager_domain_model_link',
        'tx_pxaproductmanager_domain_model_filter',
        'tx_pxaproductmanager_domain_model_producttype',
    ];
    foreach ($tablesOnStandardPages as $table) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages($table);
    }

    // Tables that has tips
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_pxaproductmanager_domain_model_product',
        'EXT:pxa_product_manager/Resources/Private/Language/locallang_csh_tx_pxaproductmanager_domain_model_product.xlf'
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_pxaproductmanager_domain_model_filter',
        'EXT:pxa_product_manager/Resources/Private/Language/locallang_csh_tx_pxaproductmanager_domain_model_filter.xlf'
    );

    // Register Datahandler hook in order to save attributes values
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['pxa_product_manager'] = \Pixelant\PxaProductManager\Hook\AttributesValuesUpdate::class;

    // Add new page type:
    $pdDokType = \Pixelant\PxaProductManager\Domain\Repository\PageRepository::DOKTYPE_PRODUCT_DISPLAY;
    $GLOBALS['PAGES_TYPES'][$pdDokType] = [
        'type' => 'web',
        'allowedTables' => '*',
    ];

})();

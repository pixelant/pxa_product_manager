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

    // Register Datahandler hook to handle product inheritance.
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['pxa_product_manager_productInheritance'] = \Pixelant\PxaProductManager\Hook\ProcessDatamap\ProductInheritanceProcessDatamap::class;

    // Register DataHandler hook to validate attribute type.
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['pxa_product_manager_attributeTypeValidation'] = \Pixelant\PxaProductManager\Hook\ProcessDatamap\AttributeTypeValidationProcessDatamap::class;

    // Register DataHandler hook to clear system cache on attribute save.
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['pxa_product_manager_attributeSave'] = \Pixelant\PxaProductManager\Hook\ProcessDatamap\AttributeSaveProcessDatamap::class;

    // Add new page type:
    $pdDokType = \Pixelant\PxaProductManager\Domain\Repository\PageRepository::DOKTYPE_PRODUCT_DISPLAY;
    $GLOBALS['PAGES_TYPES'][$pdDokType] = [
        'type' => 'web',
        'allowedTables' => '*',
    ];

    // Register solr hook to handle solr userfunc to detectSerializedValue.
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['detectSerializedValue']['pxa_product_manager']
        = \Pixelant\PxaProductManager\Hook\Solr\DetectSerializedValue::class;

})();

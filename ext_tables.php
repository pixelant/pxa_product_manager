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
})();

/*call_user_func(
    function ($_EXTKEY) {
        // Register plugin
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            $_EXTKEY,
            'Pi1',
            'Product Manager'
        );

        $tables = [
            'tx_pxaproductmanager_domain_model_product',
            'tx_pxaproductmanager_domain_model_attribute',
            'tx_pxaproductmanager_domain_model_attributeset',
            'tx_pxaproductmanager_domain_model_attributevalue',
            'tx_pxaproductmanager_domain_model_option',
            'tx_pxaproductmanager_domain_model_link',
            'tx_pxaproductmanager_domain_model_filter',
            'tx_pxaproductmanager_domain_model_order',
            'tx_pxaproductmanager_domain_model_orderconfiguration',
            'tx_pxaproductmanager_domain_model_orderformfield'
        ];

        // @codingStandardsIgnoreStart
        foreach ($tables as $table) {
            if ($table !== 'tx_pxaproductmanager_domain_model_attributevalue'
                || $table !== 'tx_pxaproductmanager_domain_model_order'
            ) {
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
                    $table,
                    'EXT:pxa_product_manager/Resources/Private/Language/locallang_csh_' . $table . '.xlf'
                );
            }

            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages($table);
        }

        // Register hooks
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][$_EXTKEY] = \Pixelant\PxaProductManager\Hook\TceMain::class;

        // Add plugin to content element wizard
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:pxa_product_manager/Configuration/TypoScript/PageTS/contentElementWizard.ts">'
        );
        // @codingStandardsIgnoreEnd

        // Link handler
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:pxa_product_manager/Configuration/TypoScript/PageTS/linkHandler.ts">'
        );

        $icons = [
            'ext-pxa-product-manager-wizard-icon' => 'package.svg',
        ];

        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );

        foreach ($icons as $identifier => $path) {
            $iconRegistry->registerIcon(
                $identifier,
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/' . $path]
            );
        }

        if (TYPO3_MODE === 'BE') {
            // Register BE module
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'Pixelant.' . $_EXTKEY,
                'web',          // Main area
                'mod1',         // Name of the module
                '',             // Position of the module
                [
                    'BackendManager' => 'index, listCategories, listProducts, listOrders, showOrder, deleteOrder, toggleOrderState'
                ],
                [          // Additional configuration
                    'access' => 'user,group',
                    'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/Extension.svg',
                    'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xml',
                ]
            );
        }
    },
    $_EXTKEY
);*/

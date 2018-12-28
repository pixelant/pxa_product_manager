<?php

use Pixelant\PxaProductManager\Backend\FormDataProvider\OrderEditFormInitialize;

defined('TYPO3_MODE') || die;

call_user_func(
    function ($_EXTKEY) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Pixelant.' . $_EXTKEY,
            'Pi1',
            [
                // @codingStandardsIgnoreStart
                'Product' => 'list, show, wishList, finishOrder, lazyList, comparePreView, compareView, groupedList, promotionList',
                // @codingStandardsIgnoreEnd
                'Navigation' => 'show',
                'AjaxProducts' => 'ajaxLazyList',
                'AjaxJson' => 'toggleWishList, toggleCompareList, loadCompareList, emptyCompareList, loadWishList',
                'Filter' => 'showFilter'
            ],
            // non-cacheable actions
            [
                'Product' => 'wishList, finishOrder, comparePreView, compareView',
                'AjaxProducts' => 'ajaxLazyList',
                'AjaxJson' => 'toggleWishList, toggleCompareList, loadCompareList, emptyCompareList, loadWishList'
            ]
        );

        // Register cart as another plugin. Otherwise it has conflict
        // with product single view
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Pixelant.' . $_EXTKEY,
            'Pi2',
            [
                'Product' => 'wishListCart, compareListCart',
            ],
            // non-cacheable actions
            [
            ]
        );

        // @codingStandardsIgnoreStart
        // Page module hook
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['pxaproductmanager_pi1']['pxa_product_manager'] =
            \Pixelant\PxaProductManager\Hook\PageLayoutView::class . '->getExtensionSummary';

        // Form data provider hook, to generate attributes TCA
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\Pixelant\PxaProductManager\Backend\FormDataProvider\ProductEditFormInitialize::class] = [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRowInitializeNew::class,
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems::class
            ]
        ];

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][OrderEditFormInitialize::class] = [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRowInitializeNew::class
            ]
        ];

        // LinkHandler
        // t3://pxappm?product=[product_id]
        // t3://pxappm?category=[category_id]
        $linkType = 'pxappm';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['linkHandler'][$linkType] = \Pixelant\PxaProductManager\LinkHandler\LinkHandling::class;
        $GLOBALS['TYPO3_CONF_VARS']['FE']['typolinkBuilder'][$linkType] = \Pixelant\PxaProductManager\LinkHandler\ProductLinkBuilder::class;
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['linkHandler'][$linkType] = \Pixelant\PxaProductManager\LinkHandler\LinkHandlingFormData::class;

        // Register cache
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_pxa_pm_categories']['frontend'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_pxa_pm_categories'] = [
                'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
                'backend' => \TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
                'options' => [
                    'defaultLifetime' => 0
                ],
                'groups' => ['all']
            ];
        }
        // @codingStandardsIgnoreEnd

        // Include page typoscript
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:pxa_product_manager/Configuration/TypoScript/PageTS/rteTsConfig.ts">'
        );

        $ppmLocalLangBe = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf';
        $productCustomSortingUpdateTask = Pixelant\PxaProductManager\Task\ProductCustomSortingUpdateTask::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][$productCustomSortingUpdateTask] = [
            'extension' => $_EXTKEY,
            'title' => $ppmLocalLangBe . ':task.productCustomSortingUpdate.title',
            'description' => $ppmLocalLangBe . ':task.productCustomSortingUpdate.description'
        ];

        // Register field control for identifier
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1534315213786] = [
            'nodeName' => 'attributeIdentifierControl',
            'priority' => 30,
            'class' => \Pixelant\PxaProductManager\Backend\FormEngine\FieldControl\AttributeIdentifierControl::class
        ];

        // Register the class to be available in 'eval' of TCA
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\Pixelant\PxaProductManager\Backend\Evaluation\LcFirstEvaluation::class] = '';

        // upgrade wizard
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'][\Pixelant\PxaProductManager\Updates\AttributesValuesUpdateTrait::$identifier]
            = \Pixelant\PxaProductManager\Utility\MainUtility::isBelowTypo3v9()
            ? \Pixelant\PxaProductManager\Updates\AttributesValuesUpdateCompatibility::class
            : \Pixelant\PxaProductManager\Updates\AttributesValuesUpdate::class;
    },
    $_EXTKEY
);

// Real url configuration
// This configuration works only together with t3kit
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('theme_t3kit')) {
    /** @noinspection PhpIncludeInspection */
    include_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('pxa_product_manager') .
        'Configuration/RealUrl/realurl.php';
}

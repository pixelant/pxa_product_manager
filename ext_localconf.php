<?php
defined('TYPO3_MODE') || die;

call_user_func(
    function ($_EXTKEY) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Pixelant.' . $_EXTKEY,
            'Pi1',
            [
                'Product' => 'list, show, wishList, lazyList, comparePreView, compareView, groupedList',
                'Navigation' => 'show',
                'AjaxProducts' => 'ajaxLazyList',
                'AjaxJson' => 'toggleWishList, toggleCompareList, loadCompareList',
                'Filter' => 'showFilter'
            ],
            // non-cacheable actions
            [
                'Product' => 'wishList, comparePreView, compareView',
                'AjaxProducts' => 'ajaxLazyList',
                'AjaxJson' => 'toggleWishList, toggleCompareList, loadCompareList'
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

        // LinkHandler
        // t3://pxappm?product=[product_id]
        // t3://pxappm?category=[category_id]
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['linkHandler']['pxappm'] = \Pixelant\PxaProductManager\LinkHandler\LinkHandling::class;
        $GLOBALS['TYPO3_CONF_VARS']['FE']['typolinkBuilder']['pxappm'] = \Pixelant\PxaProductManager\LinkHandler\ProductLinkBuilder::class;

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

        // Register solr view helper
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['PiResults']['addViewHelpers'][$_EXTKEY] =
            \Pixelant\PxaProductManager\ViewHelpers\Solr\SolrViewHelperProvider::class;
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

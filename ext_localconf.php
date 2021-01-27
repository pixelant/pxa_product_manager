<?php

defined('TYPO3_MODE') || die;

(function () {
    // Extbase
    $extbaseContainer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Extbase\Object\Container\Container::class
    );
    $extbaseContainer->registerImplementation(
        \Pixelant\PxaProductManager\Attributes\ValueMapper\MapperServiceInterface::class,
        \Pixelant\PxaProductManager\Attributes\ValueMapper\MapperService::class
    );
    $extbaseContainer->registerImplementation(
        \Pixelant\PxaProductManager\Attributes\ValueUpdater\UpdaterInterface::class,
        \Pixelant\PxaProductManager\Attributes\ValueUpdater\ValueUpdaterService::class
    );

    // Configure plugins
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Pixelant.pxa_product_manager',
        'LazyLoading',
        [
            'Api\\LazyLoading' => 'list',
        ],
        [
            'Api\\LazyLoading' => 'list',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Pixelant.pxa_product_manager',
        'LazyAvailableFilters',
        [
            'Api\\LazyAvailableFilters' => 'list',
        ],
        [
            'Api\\LazyAvailableFilters' => 'list',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Pixelant.pxa_product_manager',
        'ProductShow',
        [
            'ProductShow' => 'show'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Pixelant.pxa_product_manager',
        'ProductList',
        [
            'LazyProduct' => 'list'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Pixelant.pxa_product_manager',
        'ProductRender',
        [
            'ProductRender' => 'init'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Pixelant.pxa_product_manager',
        'CustomProductList',
        [
            'CustomProduct' => 'list'
        ]
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1608645557] = [
        'nodeName' => 'productParentValue',
        'priority' => '30',
        'class' => \Pixelant\PxaProductManager\Backend\FormEngine\FieldWizard\ParentValueFieldWizard::class,
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1609921375] = [
        'nodeName' => 'inheritedProductField',
        'priority' => '30',
        'class' => \Pixelant\PxaProductManager\Backend\FormEngine\FieldInformation\InheritedProductFieldInformation::class,
    ];

    // Add attributes fields to Product edit form
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\Pixelant\PxaProductManager\Backend\FormDataProvider\ProductFormDataProvider::class] = [
        'depends' => [
            \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRowInitializeNew::class,
            \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems::class
        ]
    ];

    // Modify data structure of flexform. Hook will dynamically load flexform parts for selected action
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing']['pxa_product_manager'] =
        \Pixelant\PxaProductManager\Hook\FlexFormDataStructureHook::class;

    // Register default plugin actions with flexform settings
    \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \Pixelant\PxaProductManager\Configuration\Flexform\Registry::class
    )->registerDefaultActions();

    // Register hook to show plugin flexform settings preview
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['pxaproductmanager_pi1']['pxa_product_manager'] =
        \Pixelant\PxaProductManager\Hook\PageLayoutView::class . '->getExtensionSummary';

    // Include page TS
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="DIR:EXT:pxa_product_manager/Configuration/TypoScript/PageTS/" extensions="ts">'
    );

    // LinkHandler
    // t3://pxappm?product=[product_id]
    // t3://pxappm?category=[category_id]
    $linkType = 'pxappm';
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['linkHandler'][$linkType]
        = \Pixelant\PxaProductManager\LinkHandler\LinkHandling::class;
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['linkHandler'][$linkType]
        = \Pixelant\PxaProductManager\LinkHandler\LinkHandlingFormData::class;
    $GLOBALS['TYPO3_CONF_VARS']['FE']['typolinkBuilder'][$linkType]
        = \Pixelant\PxaProductManager\Service\TypolinkBuilderService::class;

    // Draw header hook
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/db_layout.php']['drawHeaderHook']['pxa_product_manager']
        = \Pixelant\PxaProductManager\Hook\PageHookRelatedCategories::class . '->render';

    // Register icons
    $icons = [
        'ext-pxa-product-manager-wizard-icon' => 'package.svg',
        'apps-pagetree-productdisplay-default' => 'T3Icons/apps/apps-pagetree-productdisplay-default.svg',
        'apps-pagetree-productdisplay-hideinmenu' => 'T3Icons/apps/apps-pagetree-productdisplay-hideinmenu.svg',
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

    // Cache framework
    $cacheIdentifier = 'pm_cache_categories';
    if (! is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier] = [];
    }
    if (! isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier]['frontend'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier]['frontend']
            = \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class;
    }
    if (! isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier]['options'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier]['options']
            = ['defaultLifetime' => 0];
    }
    if (! isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier]['groups'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$cacheIdentifier]['groups']
            = ['pages', 'system'];
    }

    // Allow backend users to drag and drop the new page type:
    $pdDokType = \Pixelant\PxaProductManager\Domain\Repository\PageRepository::DOKTYPE_PRODUCT_DISPLAY;
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
        'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $pdDokType . ')'
    );
})();

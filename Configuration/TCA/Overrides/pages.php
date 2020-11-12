<?php

defined('TYPO3_MODE') || die;

(function (): void {
    $pdDokType = \Pixelant\PxaProductManager\Domain\Repository\PageRepository::DOKTYPE_PRODUCT_DISPLAY;
    // Add new page type as possible select item:
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        'pages',
        'doktype',
        [
            'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf:be.product_display_page_type',
            $pdDokType,
            'EXT:pxa_product_manager/Resources/Public/Icons/ProductListPage.svg',
        ],
        '1',
        'after'
    );

    \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
        $GLOBALS['TCA']['pages'],
        [
            // add icon for new page type:
            'ctrl' => [
                'typeicon_classes' => [
                    $pdDokType => 'apps-pagetree-productdisplay-default',
                    $pdDokType . '-hideinmenu' => 'apps-pagetree-productdisplay-hideinmenu',
                ],
            ],
            // add all page standard fields and tabs to your new page type
            'types' => [
                (string)$pdDokType => [
                    'showitem' => $GLOBALS['TCA']['pages']['types'][\TYPO3\CMS\Core\Domain\Repository\PageRepository::DOKTYPE_DEFAULT]['showitem'],
                ],
            ],
        ]
    );
})();

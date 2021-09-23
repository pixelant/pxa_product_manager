<?php

defined('TYPO3_MODE') || die('Access denied.');

return (function () {
    $ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:';

    return [
        'ctrl' => [
            'title' => $ll . 'tx_pxaproductmanager_domain_model_product',
            'label' => 'name',
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'cruser_id' => 'cruser_id',
            'dividers2tabs' => true,
            'sortby' => 'sorting',
            'versioningWS' => true,
            'origUid' => 't3_origuid',
            'languageField' => 'sys_language_uid',
            'transOrigPointerField' => 'l10n_parent',
            'transOrigDiffSourceField' => 'l10n_diffsource',
            'delete' => 'deleted',
            'enablecolumns' => [
                'disabled' => 'hidden',
                'starttime' => 'starttime',
                'endtime' => 'endtime',
            ],
            'searchFields' => 'name,sku,teaser,description',
            'thumbnail' => 'images',
            'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/product.svg',
        ],
        'types' => [
            '1' => [
                'showitem' => 'sys_language_uid,l10n_parent,l10n_diffsource, --palette--;;paletteProductType, --palette--;;general,--div--;' . $ll . 'tx_pxaproductmanager_domain_model_product.tab.attributes,attributes_values,_nonfield,
--div--;' . $ll . 'tx_pxaproductmanager_domain_model_product.tab.images, images, assets,
--div--;' . $ll . 'tx_pxaproductmanager_domain_model_product.tab.relations, parent, related_products, accessories,
--div--;' . $ll . 'tx_pxaproductmanager_domain_model_product.tab.links, fal_links, links,
--div--;' . $ll . 'tx_pxaproductmanager_domain_model_product.tab.metadata, alternative_title, meta_description, keywords,
--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, --palette--;;access',
            ],
        ],
        'palettes' => [
            'general' => ['showitem' => 'name, --linebreak--, slug, --linebreak--, singleview_page, --linebreak--, sku, --linebreak--, price, tax_rate, --linebreak--,  teaser, usp, --linebreak--, description'],
            'access' => ['showitem' => 'hidden, --linebreak--, starttime, endtime'],
            'paletteProductType' => ['showitem' => 'product_type'],
        ],
        'columns' => [
            'sys_language_uid' => [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'special' => 'languages',
                    'items' => [
                        [
                            'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                            -1,
                            'flags-multiple',
                        ],
                    ],
                    'default' => 0,
                ],
            ],
            'l10n_parent' => [
                'displayCond' => 'FIELD:sys_language_uid:>:0',
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => 'tx_pxaproductmanager_domain_model_product',
                    'size' => 1,
                    'maxitems' => 1,
                    'minitems' => 0,
                    'default' => 0,
                ],
            ],
            'l10n_diffsource' => [
                'config' => [
                    'type' => 'passthrough',
                ],
            ],
            'hidden' => [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                    'items' => [
                        [
                            0 => '',
                            1 => '',
                            'invertStateDisplay' => true,
                        ],
                    ],
                ],
            ],
            'starttime' => [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
                'config' => [
                    'type' => 'input',
                    'renderType' => 'inputDateTime',
                    'eval' => 'datetime,int',
                    'default' => 0,
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'endtime' => [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
                'config' => [
                    'type' => 'input',
                    'renderType' => 'inputDateTime',
                    'eval' => 'datetime,int',
                    'default' => 0,
                    'range' => [
                        'upper' => mktime(0, 0, 0, 1, 1, 2038),
                    ],
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'name' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.name',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim,required',
                ],
            ],
            'slug' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.slug',
                'config' => [
                    'type' => 'slug',
                    'size' => 30,
                    'generatorOptions' => [
                        'fields' => ['name'],
                        'replacements' => [
                            '/' => '-',
                        ],
                    ],
                    'fallbackCharacter' => '-',
                    'eval' => 'uniqueInSite',
                    'default' => '',
                ],
            ],
            'sku' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.sku',
                'config' => [
                    'type' => 'input',
                    'size' => 15,
                    'eval' => 'trim',
                ],
            ],
            'price' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.price',
                'config' => [
                    'type' => 'input',
                    'size' => 5,
                    'eval' => 'double2',
                ],
            ],
            'tax_rate' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.tax_rate',
                'config' => [
                    'type' => 'input',
                    'size' => 5,
                    'range' => [
                        'lower' => 0,
                        'upper' => 100,
                    ],
                    'eval' => 'double2',
                ],
            ],
            'description' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.description',
                'config' => [
                    'type' => 'text',
                    'enableRichtext' => true,
                    'richtextConfiguration' => 'default',
                    'fieldControl' => [
                        'fullScreenRichtext' => [
                            'disabled' => false,
                        ],
                    ],
                    'cols' => 40,
                    'rows' => 15,
                    'eval' => 'trim',
                ],
            ],
            'attributes_values' => [
                'exclude' => false,
                'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_attributevalue',
                    'foreign_field' => 'product',
                    'maxitems' => 9999,
                    'foreign_types' => [
                        ['showitem' => 'value'],
                    ],
                    'appearance' => [
                        'collapseAll' => 0,
                        'levelLinksPosition' => 'none',
                        'showSynchronizationLink' => 1,
                        'showPossibleLocalizationRecords' => 1,
                        'showAllLocalizationLink' => 1,
                        'enabledControls' => [
                            'info' => false,
                            'new' => false,
                            'dragdrop' => false,
                            'sort' => false,
                            'hide' => false,
                            'delete' => false,
                            'localize' => false,
                        ],
                    ],
                ],
            ],
            'related_products' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.related_products',
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => 'tx_pxaproductmanager_domain_model_product',
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_product',
                    'suggestOptions' => [
                        'tx_pxaproductmanager_domain_model_product' => \Pixelant\PxaProductManager\Utility\TcaUtility::getRelatedProductsForeignTableWherePid() .
                            ' AND tx_pxaproductmanager_domain_model_product.uid != ###THIS_UID###' .
                            ' ORDER BY tx_pxaproductmanager_domain_model_product.name',
                    ],
                    'MM' => 'tx_pxaproductmanager_product_product_mm',
                    'MM_match_fields' => [
                        'tablenames' => 'tx_pxaproductmanager_domain_model_product',
                        'fieldname' => 'related_products',
                    ],
                    'MM_opposite_field' => 'related_products',
                    'MM_oppositeUsage' => [
                        'tx_pxaproductmanager_domain_model_product' => ['accessories'],
                    ],
                    'size' => 10,
                    'minitems' => 0,
                    'maxitems' => 9999,
                    'multiple' => 0,
                ],
            ],
            'images' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.images',
                'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'images',
                    [
                        'appearance' => [
                            'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                            'collapseAll' => true,
                        ],
                        'overrideChildTca' => [
                            'types' => [
                                \TYPO3\CMS\Core\Resource\File::FILETYPE_UNKNOWN => [
                                    'showitem' => '
                                --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.basicoverlayPalette;basicoverlayPalette,
                                --palette--;;filePalette',
                                ],
                                \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                    'showitem' => '
                                --palette--;;pxaProductManagerPalette,
                                --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette',
                                ],
                            ],
                        ],
                        'maxitems' => 99,
                    ],
                    $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
                ),
            ],
            'fal_links' => [
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.fal_links',
                'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'fal_links',
                    [
                        'appearance' => [
                            'createNewRelationLinkTitle' => $ll . 'tx_pxaproductmanager_domain_model_product.fal_links.add_button',
                            'collapseAll' => true,
                        ],
                        'behaviour' => [
                            'allowLanguageSynchronization' => true,
                        ],
                        'foreign_types' => [
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_UNKNOWN => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.basicoverlayPalette;basicoverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.basicoverlayPalette;basicoverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.audioOverlayPalette;audioOverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.videoOverlayPalette;videoOverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.basicoverlayPalette;basicoverlayPalette,
                            --palette--;;filePalette',
                            ],
                        ],
                        'maxitems' => 99,
                    ]
                ),
            ],
            'links' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.links',
                'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_link',
                    'foreign_field' => 'product',
                    'foreign_sortby' => 'sorting',
                    'maxitems' => 9999,
                    'appearance' => [
                        'collapseAll' => true,
                        'levelLinksPosition' => 'top',
                        'showSynchronizationLink' => true,
                        'showPossibleLocalizationRecords' => true,
                        'showAllLocalizationLink' => true,
                    ],
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'parent' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.parent',
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => 'tx_pxaproductmanager_domain_model_product',
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_product',
                    'size' => 1,
                    'maxitems' => 1,
                    'minitems' => 0,
                    'default' => 0,
                    'eval' => 'int',
                    'fieldControl' => [
                        'editPopup' => [
                            'disabled' => true,
                        ],
                        'addRecord' => [
                            'disabled' => true,
                        ],
                    ],
                    'suggestOptions' => [
                        'tx_pxaproductmanager_domain_model_product' => [
                            'searchCondition' => 'sys_language_uid in (0,-1) AND parent = 0',
                        ],
                    ],
                ],
            ],
            'keywords' => [
                'exclude' => true,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.keywords',
                'config' => [
                    'type' => 'text',
                    'cols' => 30,
                    'rows' => 5,
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'meta_description' => [
                'exclude' => true,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.meta_description',
                'config' => [
                    'type' => 'text',
                    'cols' => 30,
                    'rows' => 5,
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'alternative_title' => [
                'exclude' => true,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.alternative_title',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                ],
            ],
            'assets' => [
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.assets',
                'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'assets',
                    [
                        'appearance' => [
                            'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                            'collapseAll' => true,
                        ],
                        'behaviour' => [
                            'allowLanguageSynchronization' => true,
                        ],
                        'foreign_types' => [
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_UNKNOWN => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.basicoverlayPalette;basicoverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.basicoverlayPalette;basicoverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.audioOverlayPalette;audioOverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.videoOverlayPalette;videoOverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                                'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.basicoverlayPalette;basicoverlayPalette,
                            --palette--;;filePalette',
                            ],
                        ],
                    ],
                ),
            ],
            'teaser' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.teaser',
                'config' => [
                    'type' => 'text',
                    'cols' => 40,
                    'rows' => 5,
                    'eval' => 'trim',
                ],
            ],
            'usp' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.usp',
                'config' => [
                    'type' => 'text',
                    'cols' => 40,
                    'rows' => 5,
                    'eval' => 'trim',
                ],
            ],
            'product_type' => [
                'exclude' => false,
                'onChange' => 'reload',
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.product_type',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        ['', 0],
                    ],
                    'default' => 0,
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_producttype',
                    'minitems' => 0,
                    'maxitems' => 1,
                ],
            ],
            'singleview_page' => [
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.singleview_page',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectMultipleSideBySide',
                    'foreign_table' => 'pages',
                    // 'foreign_table_where' => 'pages.doktype = 9 ORDER BY pages.sorting',
                    'MM' => 'tx_pxaproductmanager_product_pages_mm',
                    'MM_match_fields' => [
                        'tablenames' => 'pages',
                        'fieldname' => 'doktype',
                    ],
                    'treeConfig' => [
                        'parentField' => 'pid',
                        'appearance' => [
                            'expandAll' => true,
                            'showHeader' => true,
                        ],
                    ],
                ],
            ],
            'accessories' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_product.accessories',
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => 'tx_pxaproductmanager_domain_model_product',
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_product',
                    'suggestOptions' => [
                        'tx_pxaproductmanager_domain_model_product' => \Pixelant\PxaProductManager\Utility\TcaUtility::getRelatedProductsForeignTableWherePid() .
                            ' AND tx_pxaproductmanager_domain_model_product.uid != ###THIS_UID###' .
                            ' ORDER BY tx_pxaproductmanager_domain_model_product.name',
                    ],
                    'MM' => 'tx_pxaproductmanager_product_product_mm',
                    'MM_match_fields' => [
                        'tablenames' => 'tx_pxaproductmanager_domain_model_product',
                        'fieldname' => 'accessories',
                    ],
                    'MM_opposite_field' => 'accessories',
                    'MM_oppositeUsage' => [
                        'tx_pxaproductmanager_domain_model_product' => ['accessories'],
                    ],
                    'size' => 10,
                    'minitems' => 0,
                    'maxitems' => 9999,
                    'multiple' => 0,
                ],
            ],
            'crdate' => [
                'label' => 'crdate',
                'config' => [
                    'type' => 'input',
                    'renderType' => 'inputDateTime',
                    'eval' => 'datetime,int',
                    'readOnly' => true,
                ],
            ],
            'tstamp' => [
                'label' => 'tstamp',
                'config' => [
                    'type' => 'input',
                    'renderType' => 'inputDateTime',
                    'eval' => 'datetime,int',
                    'readOnly' => true,
                ],
            ],
            'deleted' => [
                'label' => 'deleted',
                'config' => [
                    'type' => 'passthrough',
                ],
            ],
        ],
    ];
})();

<?php
defined('TYPO3_MODE') || die;

(function () {
    $ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:';

    $tempColumns = [
        'pxapm_keywords' => [
            'exclude' => 1,
            'label' => $GLOBALS['TCA']['pages']['columns']['keywords']['label'],
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ]
        ],
        'pxapm_meta_description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.description_formlabel',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ]
        ],
        'pxapm_alternative_title' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.alternative_title',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ]
        ],
        'pxapm_slug' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:pages.slug',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => ['title'],
                    'replacements' => [
                        '/' => '-'
                    ],
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInPid',
                'default' => '',
            ]
        ],
        'pxapm_image' => [
            'exclude' => true,
            'label' => $ll . 'sys_category.pxapm_image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'pxapm_image',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                    ],
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                    'foreign_types' => [
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_UNKNOWN => [
                            'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.basicoverlayPalette;basicoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                    ],
                    'maxitems' => 1
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ],
        /* This field is mainly used for sorting */
        'pxapm_subcategories' => [
            'label' => $ll . 'sys_category.pxapm_subcategories',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'sys_category',
                'foreign_field' => 'parent',
                'foreign_sortby' => 'sorting',
                'maxitems' => 9999,
                'behaviour' => [],
                'appearance' => [
                    'levelLinksPosition' => 'none',
                    'collapseAll' => true,
                    'useSortable' => true,
                    'enabledControls' => [
                        'new' => false,
                        'info' => false,
                        'hide' => false,
                        'delete' => false,
                        'localize' => false,
                    ]
                ]
            ]
        ],
        'pxapm_products' => [
            'label' => 'Products list',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_product',
                'MM' => 'sys_category_record_mm',
                'MM_match_fields' => [
                    'tablenames' => 'tx_pxaproductmanager_domain_model_product',
                    'fieldname' => 'categories',
                ],
                'maxitems' => 9999,
            ]
        ],
        'pxapm_attributes_sets' => [
            'exclude' => 0,
            'label' => $ll . 'sys_category.pxapm_attributes_sets',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_attributeset',
                'foreign_table_where' => \Pixelant\PxaProductManager\Utility\TcaUtility::getAttributesSetsForeignTableWherePid() .
                    ' ORDER BY tx_pxaproductmanager_domain_model_attributeset.sorting',
                'MM' => 'tx_pxaproductmanager_attributeset_record_mm',
                'MM_match_fields' => [
                    'tablenames' => 'sys_category',
                    'fieldname' => 'categories',
                ],
                'MM_opposite_field' => 'categories',
                'size' => 10,
                'autoSizeMax' => 30,
                'maxitems' => 9999,
                'multiple' => 0,
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ]
                ]
            ]
        ],
        'pxapm_description' => [
            'exclude' => 0,
            'label' => $ll . 'sys_category.pxapm_description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'enableRichtext' => true,
                'fieldControl' => [
                    'fullScreenRichtext' => [
                        'disabled' => false
                    ]
                ]
            ]
        ],
        'pxapm_banner_image' => [
            'exclude' => 1,
            'label' => $ll . 'sys_category.pxapm_banner_image',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'pxapm_banner_image',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
                    ],
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                    'foreign_types' => [
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_UNKNOWN => [
                            'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.basicoverlayPalette;basicoverlayPalette,
                            --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                        ],
                    ],
                    'maxitems' => 1
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ],
        'pxapm_tax_rate' => [
            'exclude' => 0,
            'label' => $ll . 'sys_category.pxapm_tax_rate',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'range' => [
                    'lower' => 0,
                    'upper' => 100,
                ],
                'eval' => 'double2',
                'default' => 0.00
            ],
        ],
        'pxapm_content_page' => [
            'exclude' => 1,
            'onChange' => 'reload',
            'label' => $ll . 'sys_category.pxapm_content_page',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'foreign_table' => 'pages',
                'default' => 0,
                'size' => 1,
                'max_size' => 1,
            ],
        ],
        'pxapm_content_page_link' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:pxapm_content_page:>:0',
            'label' => $ll . 'sys_category.pxapm_content_page_link',
            'config' => [
                'type' => 'user',
                'userFunc' => \Pixelant\PxaProductManager\UserFunction\TCA\CategoryUserFunction::class . '->pageModuleLinkField',
            ],
        ],
        'pxapm_content_colpos' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:pxapm_content_page:>:0',
            'label' => $ll . 'sys_category.pxapm_content_colpos',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'default' => 0
            ]
        ],
        'pxapm_hidden_in_navigation' => [
            'exclude' => true,
            'label' => $ll . 'sys_category.pxapm_hidden_in_navigation',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
            ]
        ],
        'pxapm_hide_products' => [
            'exclude' => true,
            'label' => $ll . 'sys_category.pxapm_hide_products',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
            ]
        ],
        'pxapm_hide_subcategories' => [
            'exclude' => true,
            'label' => $ll . 'sys_category.pxapm_hide_subcategories',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
            ]
        ],
    ];
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_category', $tempColumns);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_category', 'pxaProductManagerAssets', 'pxapm_image, --linebreak--, pxapm_banner_image');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_category', 'pxaProductManagerNavigation', 'pxapm_hidden_in_navigation, pxapm_hide_products, pxapm_hide_subcategories');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_category', 'pxaProductManagerContent', 'pxapm_content_page, --linebreak--, pxapm_content_colpos, pxapm_content_page_link');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_category', 'pxaProductManagerSeo', 'pxapm_alternative_title, --linebreak--, pxapm_meta_description, pxapm_keywords');

    // Additional fields
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'sys_category',
        '--div--;' . $ll . 'sys_category.additional_fields_tab, --palette--;' . $ll . 'sys_category.palette.navigation;pxaProductManagerNavigation,
         --palette--;' . $ll . 'sys_category.palette.assets;pxaProductManagerAssets, pxapm_tax_rate, pxapm_description,
        --div--;' . $ll . 'sys_category.metadata_tab, --palette--;;pxaProductManagerSeo,
        --div--;' . $ll . 'sys_category.content_tab, --palette--;;pxaProductManagerContent,
        --div--;' . $ll . 'sys_category.attributes_tab, pxapm_attributes_sets,
        --div--;' . $ll . 'sys_category.subcategories_tab, pxapm_subcategories',
        '',
        'after:items'
    );

    // Slug
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'sys_category',
        'pxapm_slug',
        '',
        'after:title'
    );

    if (!empty($categoryWhere = \Pixelant\PxaProductManager\Utility\TcaUtility::getCategoriesTCAWhereClause())) {
        $config = &$GLOBALS['TCA']['sys_category']['columns']['parent']['config'];
        $config['foreign_table_where'] = $categoryWhere . ' ' . $config['foreign_table_where'];
    }
})();

<?php

$ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => $ll . 'tx_pxaproductmanager_domain_model_product',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,

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
        'searchFields' => 'name,sku',
        'thumbnail' => 'images',
        'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/product.svg'
    ],
    // @codingStandardsIgnoreStart
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, sku, price, teaser, description, usp, additional_information, attributes_description, import_id, import_name, disable_single_view, related_products, images, links, fal_links, sub_products,meta_description, keywords, alternative_title, path_segment, serialized_attributes_values, attribute_images, attribute_values',
    ],

    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,--palette--;;1,name, sku, price, teaser, description, usp, additional_information, attributes_description, launched, discontinued, custom_sorting,
--div--;LLL:EXT:lang/locallang_tca.xlf:sys_category.tabs.category, categories,
--palette--;;paletteAttributes,
--div--;' . $ll . 'tx_pxaproductmanager_domain_model_product.tab.images, images,
--div--;' . $ll . 'tx_pxaproductmanager_domain_model_product.tab.assets, assets,
--div--;' . $ll . 'tx_pxaproductmanager_domain_model_product.tab.relations, related_products, sub_products, accessories, fal_links, links,
--div--;' . $ll . 'tx_pxaproductmanager_domain_model_product.tab.metadata, meta_description, keywords, alternative_title, path_segment,
--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, disable_single_view, starttime, endtime'
// @codingStandardsIgnoreEnd
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
        'paletteAttributes' => ['showitem' => '']
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ],
                ],
                'default' => 0,
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_pxaproductmanager_domain_model_product',
                'foreign_table_where' => 'AND tx_pxaproductmanager_domain_model_product.pid=###CURRENT_PID###' .
                    ' AND tx_pxaproductmanager_domain_model_product.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'size' => 13,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'size' => 13,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'name' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'sku' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.sku',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'price' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.price',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'eval' => 'double2'
            ],
        ],
        'description' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'enableRichtext' => true
            ]
        ],
        'import_id' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.import_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'import_name' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.import_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'disable_single_view' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.disable_single_view',
            'config' => [
                'type' => 'check',
                'default' => 0
            ],
        ],
        'attribute_values' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.attribute_values',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_attributevalue',
                'foreign_field' => 'product',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
                'behaviour' => [
                    'localizeChildrenAtParentLocalization' => true,
                ]
            ]
        ],
        'related_products' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.related_products',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_product',
                'foreign_table_where' => \Pixelant\PxaProductManager\Utility\TCAUtility::getRelatedProductsForeignTableWherePid() .
                    ' AND tx_pxaproductmanager_domain_model_product.uid != ###THIS_UID###' .
                    ' AND tx_pxaproductmanager_domain_model_product.sys_language_uid IN (-1,0)' .
                    ' ORDER BY tx_pxaproductmanager_domain_model_product.name',
                'MM' => 'tx_pxaproductmanager_product_product_mm',
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
        'images' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.images',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'images',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' =>
                            'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                        'showPossibleLocalizationRecords' => false,
                        'showRemovedLocalizationRecords' => true,
                        'showAllLocalizationLink' => false,
                        'showSynchronizationLink' => false
                    ],
                    'foreign_match_fields' => [
                        'fieldname' => 'images',
                        'tablenames' => 'tx_pxaproductmanager_domain_model_product',
                        'table_local' => 'sys_file',
                    ],
                    // @codingStandardsIgnoreStart
                    'overrideChildTca' => [
                        'types' => [
                            '0' => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerPalette,
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerPalette,
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerPalette,
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerPalette,
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerPalette,
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerPalette,
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ]
                        ]
                    ],
                    // @codingStandardsIgnoreEnd
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                        'localizeChildrenAtParentLocalization' => true,
                    ],
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ],
        'attribute_images' => [
            'exclude' => 0,
            'label' => '',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'attribute_images',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' =>
                            'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                        'showPossibleLocalizationRecords' => false,
                        'showRemovedLocalizationRecords' => true,
                        'showAllLocalizationLink' => false,
                        'showSynchronizationLink' => false
                    ],
                    'foreign_match_fields' => [
                        'fieldname' => 'attribute_images',
                        'tablenames' => 'tx_pxaproductmanager_domain_model_product',
                        'table_local' => 'sys_file',
                    ],
                    // @codingStandardsIgnoreStart
                    'overrideChildTca' => [
                        'types' => [
                            '0' => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerPalette,
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerPalette,
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerPalette,
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerPalette,
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerPalette,
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerPalette,
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                            ]
                        ]
                    ]
                ],
                // @codingStandardsIgnoreEnd
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ],
        'fal_links' => [
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.fal_links',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'fal_links',
                [
                    'behaviour' => [
                        'localizeChildrenAtParentLocalization' => true,
                        'allowLanguageSynchronization' => true
                    ],
                    'appearance' => [
                        'createNewRelationLinkTitle' => $ll .
                            'tx_pxaproductmanager_domain_model_product.fal_links.add_button',
                        'fileUploadAllowed' => false,
                    ],
                    'overrideChildTca' => [
                        'types' => [
                            '0' => [
                                'showitem' => '
                    --palette--;;basicoverlayPalette,
                    --palette--;;filePalette,
                    '
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                                'showitem' => '
                    --palette--;;basicoverlayPalette,
                    --palette--;;filePalette,
                    '
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                    --palette--;;basicoverlayPalette,
                    --palette--;;filePalette,
                    '
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                                'showitem' => '
                    --palette--;;basicoverlayPalette,
                    --palette--;;filePalette,
                    '
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                                'showitem' => '
                    --palette--;;basicoverlayPalette,
                    --palette--;;filePalette,
                    '
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                                'showitem' => '
                    --palette--;;basicoverlayPalette,
                    --palette--;;filePalette,
                    '
                            ]
                        ]
                    ]
                ]
            )
        ],
        'links' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.links',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_link',
                'foreign_field' => 'product',
                'foreign_sortby' => 'sorting',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 1,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
                'behaviour' => [
                    'localizeChildrenAtParentLocalization' => true,
                ],
            ],
        ],
        'sub_products' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.sub_products',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_product',
                'foreign_table_where' =>  \Pixelant\PxaProductManager\Utility\TCAUtility::getSubProductsForeignTableWherePid() .
                    ' AND tx_pxaproductmanager_domain_model_product.uid != ###THIS_UID###' .
                    ' AND tx_pxaproductmanager_domain_model_product.sys_language_uid IN (-1,0)' .
                    ' ORDER BY tx_pxaproductmanager_domain_model_product.name',
                'MM' => 'tx_pxaproductmanager_product_subproducts_product_mm',
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
        'keywords' => [
            'exclude' => 1,
            'label' => $GLOBALS['TCA']['pages']['columns']['keywords']['label'],
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
            ]
        ],
        'meta_description' => [
            'exclude' => 1,
            'label' => $GLOBALS['TCA']['pages']['columns']['description']['label'],
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ],
            ]
        ],
        'alternative_title' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.alternative_title',
            'config' => [
                'type' => 'input',
                'size' => 30
            ]
        ],
        'path_segment' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.path_segment',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'nospace,alphanum_x,lower',
            ]
        ],
        'serialized_attributes_values' => [
            'exclude' => 1,
            'label' => 'serialized_attributes_values',
            'config' => [
                'type' => 'text'
            ]
        ],
        'crdate' => [
            'label' => 'crdate',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'tstamp' => [
            'label' => 'tstamp',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'deleted' => [
            'label' => 'deleted',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'attributes_description' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.attributes_description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'enableRichtext' => true
            ]
        ],
        'assets' => [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/Database.xlf:tt_content.asset_references',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('assets', [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/Database.xlf:tt_content.asset_references.addFileReference'
                ],
                // custom configuration for displaying fields in the overlay/reference table
                // behaves the same as the image field.
                'overrideChildTca' => [
                    'types' => [
                        '0' => [
                            'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                            'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                            'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.audioOverlayPalette;audioOverlayPalette,
                                --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                            'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.videoOverlayPalette;videoOverlayPalette,
                                --palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                            'showitem' => '
                                --palette--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                                --palette--;;filePalette'
                        ]
                    ],
                ],
            ], $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext'])
        ],
        'additional_information' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.additional_information',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'enableRichtext' => true
            ]
        ],
        'teaser' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.teaser',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 5,
                'eval' => 'trim',
                'enableRichtext' => false
            ]
        ],
        'usp' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.usp',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 10,
                'eval' => 'trim',
                'enableRichtext' => false
            ]
        ],
        'launched' => [
            'exclude' => 0,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.launched',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'date',
                'size' => 13,
                'default' => 0,
            ],
        ],
        'discontinued' => [
            'exclude' => 0,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.discontinued',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'date',
                'size' => 13,
                'default' => 0,
            ],
        ],
        'accessories' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.accessories',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_product',
                'foreign_table_where' => \Pixelant\PxaProductManager\Utility\TCAUtility::getAccessoriesForeignTableWherePid() .
                    ' AND tx_pxaproductmanager_domain_model_product.uid != ###THIS_UID###' .
                    ' AND tx_pxaproductmanager_domain_model_product.sys_language_uid IN (-1,0)' .
                    ' ORDER BY tx_pxaproductmanager_domain_model_product.name',
                'MM' => 'tx_pxaproductmanager_product_accessories_product_mm',
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
        'custom_sorting' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_product.custom_sorting',
            'config' => [
                'type' => 'input',
                'readOnly' => '1',
                'eval' => 'int',
                'size' => 13,
                'default' => 0,
            ],
        ]
    ]
];

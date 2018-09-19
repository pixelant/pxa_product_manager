<?php
defined('TYPO3_MODE') || die;

$ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:';
$llType = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_attribute.type_';
$accessTab = '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime';
$llCore = \Pixelant\PxaProductManager\Utility\TCAUtility::getCoreLLPath();

$tx_pxaproductmanager_domain_model_attribute = [
    'ctrl' => [
        'title' => $ll . 'tx_pxaproductmanager_domain_model_attribute',
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

        'type' => 'type',

        'searchFields' => 'name,type,required,show_in_attribute_listing,identifier,options,',
        'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/tag.svg'
    ],
    // @codingStandardsIgnoreStart
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, label, type, required, show_in_attribute_listing, show_in_compare, identifier, icon, default_value, options, label_unchecked, label_checked',
    ],
    'types' => [
        '1' => ['showitem' => '--palette--;;core, --palette--;;common, --palette--;' . $ll . 'palette.options;options, identifier, default_value,' . $accessTab],
        '4' => ['showitem' => '--palette--;;core, --palette--;;common, --palette--;' . $ll . 'palette.options;options, identifier, default_value, options,' . $accessTab],
        '9' => ['showitem' => '--palette--;;core, --palette--;;common, --palette--;' . $ll . 'palette.options;options, identifier, default_value, options,' . $accessTab],
        '5' => ['showitem' => '--palette--;;core, --palette--;;common, --palette--;' . $ll . 'palette.checkbox_values;checkbox_values, --palette--;' . $ll . 'palette.options;options, identifier, default_value,' . $accessTab],
    ],
    // @codingStandardsIgnoreEnd
    'palettes' => [
        'core' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, --linebreak--, hidden'],
        'common' => ['showitem' => 'name, --linebreak--, label, --linebreak--, type'],
        'options' => ['showitem' => 'required, show_in_attribute_listing, show_in_compare, --linebreak--, icon'],
        'checkbox_values' => ['showitem' => 'label_checked, label_unchecked'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => $llCore . 'locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        $llCore . 'locallang_general.xlf:LGL.allLanguages',
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
            'label' => $llCore . 'locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_pxaproductmanager_domain_model_attribute',
                'foreign_table_where' => 'AND tx_pxaproductmanager_domain_model_attribute.pid=###CURRENT_PID###' .
                    ' AND tx_pxaproductmanager_domain_model_attribute.sys_language_uid IN (-1,0)',
                'default' => 0
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => $llCore . 'locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ]
        ],
        'starttime' => [
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => $llCore . 'locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'size' => 13,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ]
        ],
        'endtime' => [
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => $llCore . 'locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'size' => 13,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ]
        ],
        'name' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ]
        ],
        'label' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ]
        ],
        'type' => [
            'exclude' => 0,
            'onChange' => 'reload',
            'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['-- Label --', 0],
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => 'required'
            ]
        ],
        'required' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.required',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
        'show_in_attribute_listing' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.show_in_attribute_listing',
            'config' => [
                'type' => 'check',
                'default' => 1
            ]
        ],
        'show_in_compare' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.show_in_compare',
            'config' => [
                'type' => 'check',
                'default' => 1
            ]
        ],
        'identifier' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.identifier',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,alpha,nospace,unique',
                'fieldControl' => [
                    'attributeIdentifierControl' => [
                        'renderType' => 'attributeIdentifierControl'
                    ]
                ]
            ]
        ],
        'default_value' => [
            'exclude' => 1,
            'displayCond' => [
                'AND' => [
                    'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DROPDOWN,
                    'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_CHECKBOX,
                    'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_MULTISELECT,
                    'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_IMAGE,
                    'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_FILE,
                    'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_LINK,
                    'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DATETIME,
                ]
            ],
            'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.default_value',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ]
        ],
        'label_checked' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:type:=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_CHECKBOX,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.label_checked',
            'config' => [
                'type' => 'input',
                'size' => 15,
                'eval' => 'trim'
            ]
        ],
        'label_unchecked' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:type:=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_CHECKBOX,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.label_unchecked',
            'config' => [
                'type' => 'input',
                'size' => 15,
                'eval' => 'trim'
            ]
        ],
        'options' => [
            'exclude' => 0,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.options',
            'displayCond' => 'FIELD:type:IN:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DROPDOWN .
                ',' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_MULTISELECT . '',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_option',
                'foreign_field' => 'attribute',
                'foreign_sortby' => 'sorting',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 1,
                    'levelLinksPosition' => 'bottom',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1,
                    'useSortable' => 1
                ]
            ]
        ],
        'icon' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.icon',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'icon',
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
                        'fieldname' => 'icon',
                        'tablenames' => 'tx_pxaproductmanager_domain_model_attribute',
                        'table_local' => 'sys_file',
                    ],
                    'maxitems' => 1,
                    // @codingStandardsIgnoreStart
                    'overrideChildTca' => [
                        'types' => [
                            '0' => [
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
                            ]
                        ]
                    ],
                    // @codingStandardsIgnoreEnd
                    'behaviour' => [
                        'allowLanguageSynchronization' => true
                    ],
                ],
                'svg'
            ),
        ],
    ]
];


$tx_pxaproductmanager_domain_model_attribute['columns']['type']['config']['items'] = [
    [
        $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_INPUT,
        \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_INPUT
    ],
    [
        $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_TEXT,
        \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_TEXT
    ],
    [
        $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DATETIME,
        \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DATETIME
    ],
    [
        $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DROPDOWN,
        \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DROPDOWN
    ],
    [
        $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_MULTISELECT,
        \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_MULTISELECT
    ],
    [
        $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_CHECKBOX,
        \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_CHECKBOX
    ],
    [
        $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_LINK,
        \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_LINK
    ],
    [
        $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_IMAGE,
        \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_IMAGE
    ],
    [
        $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_FILE,
        \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_FILE
    ],
    [
        $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_LABEL,
        \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_LABEL
    ],
];

unset($ll, $llType, $accessTab);

return $tx_pxaproductmanager_domain_model_attribute;

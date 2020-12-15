<?php

defined('TYPO3_MODE') || die;

return (function () {
    $ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:';
    $llType = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_attribute.type_';

    return [
        'ctrl' => [
            'title' => $ll . 'tx_pxaproductmanager_domain_model_attribute',
            'label' => 'name',
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'cruser_id' => 'cruser_id',
            'dividers2tabs' => true,
            'sortby' => 'sorting',
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

            'searchFields' => 'name, label, label_checked, label_unchecked',
            'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/tag.svg',
        ],

        'interface' => [
            'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, label, type, required, show_in_attribute_listing, show_in_compare, identifier, default_value, options, label_checked, label_unchecked, starttime, endtime',
        ],
        'types' => [
            '1' => ['showitem' => '--palette--;;core, --palette--;;common, --palette--;' . $ll . 'palette.options;options, identifier, default_value, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
            '4' => ['showitem' => '--palette--;;core, --palette--;;common, --palette--;' . $ll . 'palette.options;options, identifier, default_value, options, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
            '9' => ['showitem' => '--palette--;;core, --palette--;;common, --palette--;' . $ll . 'palette.options;options, identifier, default_value, options, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
            '5' => ['showitem' => '--palette--;;core, --palette--;;common, --palette--;' . $ll . 'palette.checkbox_values;checkbox_values, --palette--;' . $ll . 'palette.options;options, identifier, default_value, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
        ],

        'palettes' => [
            'core' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, --linebreak--, hidden'],
            'common' => ['showitem' => 'name, --linebreak--, label, --linebreak--, type'],
            'options' => ['showitem' => 'required, show_in_attribute_listing, show_in_compare, --linebreak--, image'],
            'checkbox_values' => ['showitem' => 'label_checked, label_unchecked'],
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
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        ['', 0],
                    ],
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_attribute',
                    'foreign_table_where' => 'AND tx_pxaproductmanager_domain_model_attribute.pid=###CURRENT_PID###' .
                        ' AND tx_pxaproductmanager_domain_model_attribute.sys_language_uid IN (-1,0)',
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
                'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.name',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim,required',
                ],
            ],
            'label' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.label',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim',
                ],
            ],
            'type' => [
                'exclude' => false,
                'onChange' => 'reload',
                'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.type',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        [
                            $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_INPUT,
                            \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_INPUT,
                        ],
                        [
                            $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_TEXT,
                            \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_TEXT,
                        ],
                        [
                            $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DATETIME,
                            \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DATETIME,
                        ],
                        [
                            $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DROPDOWN,
                            \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DROPDOWN,
                        ],
                        [
                            $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_MULTISELECT,
                            \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_MULTISELECT,
                        ],
                        [
                            $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_CHECKBOX,
                            \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_CHECKBOX,
                        ],
                        [
                            $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_LINK,
                            \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_LINK,
                        ],
                        [
                            $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_IMAGE,
                            \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_IMAGE,
                        ],
                        [
                            $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_FILE,
                            \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_FILE,
                        ],
                        [
                            $llType . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_LABEL,
                            \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_LABEL,
                        ],
                    ],
                    'size' => 1,
                    'maxitems' => 1,
                    'eval' => 'required',
                ],
            ],
            'required' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.required',
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                    'items' => [
                        [
                            0 => '',
                            1 => '',
                        ],
                    ],
                    'default' => 0,
                ],
            ],
            'show_in_attribute_listing' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.show_in_attribute_listing',
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                    'items' => [
                        [
                            0 => '',
                            1 => '',
                        ],
                    ],
                    'default' => 1,
                ],
            ],
            'show_in_compare' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.show_in_compare',
                'config' => [
                    'type' => 'check',
                    'renderType' => 'checkboxToggle',
                    'items' => [
                        [
                            0 => '',
                            1 => '',
                        ],
                    ],
                    'default' => 1,
                ],
            ],
            'identifier' => [
                'exclude' => true,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.identifier',
                'l10n_mode' => 'exclude',
                'l10n_display' => 'defaultAsReadonly',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim,alphanum,nospace,uniqueInPid',
                    'fieldControl' => [
                        'attributeIdentifierControl' => [
                            'renderType' => 'attributeIdentifierControl',
                        ],
                    ],
                ],
            ],
            'default_value' => [
                'exclude' => true,
                'displayCond' => [
                    'AND' => [
                        'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DROPDOWN,
                        'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_CHECKBOX,
                        'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_MULTISELECT,
                        'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_IMAGE,
                        'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_FILE,
                        'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_LINK,
                        'FIELD:type:!=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DATETIME,
                    ],
                ],
                'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.default_value',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim',
                ],
            ],
            'label_checked' => [
                'exclude' => true,
                'displayCond' => 'FIELD:type:=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_CHECKBOX,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.label_checked',
                'config' => [
                    'type' => 'input',
                    'size' => 15,
                    'eval' => 'trim',
                ],
            ],
            'label_unchecked' => [
                'exclude' => true,
                'displayCond' => 'FIELD:type:=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_CHECKBOX,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.label_unchecked',
                'config' => [
                    'type' => 'input',
                    'size' => 15,
                    'eval' => 'trim',
                ],
            ],
            'options' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.options',
                'displayCond' => [
                    'OR' => [
                        'FIELD:type:=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DROPDOWN,
                        'FIELD:type:=:' . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_MULTISELECT,
                    ],
                ],
                'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_option',
                    'foreign_field' => 'attribute',
                    'foreign_sortby' => 'sorting',
                    'maxitems' => 9999,
                    'appearance' => [
                        'collapseAll' => true,
                        'levelLinksPosition' => 'bottom',
                        'showSynchronizationLink' => true,
                        'showPossibleLocalizationRecords' => true,
                        'showAllLocalizationLink' => true,
                        'useSortable' => true,
                    ],
                ],
            ],
            'image' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_attribute.image',
                'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'image',
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
                            'fieldname' => 'image',
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
                    'jpeg,jpg,svg'
                ),
            ],
        ],
    ];
})();

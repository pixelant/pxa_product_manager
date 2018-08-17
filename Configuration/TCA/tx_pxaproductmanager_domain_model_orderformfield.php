<?php

$ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_orderformfield';
$llCore = \Pixelant\PxaProductManager\Utility\TCAUtility::getCoreLLPath();

return [
    'ctrl' => [
        'title' => $ll,
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
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
        'sortby' => 'sorting',
        'hideTable' => 1,
        'searchFields' => 'name',
        'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/form-field.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, label, user_email_field, type',
    ],
    'types' => [
        \Pixelant\PxaProductManager\Domain\Model\OrderFormField::FIELD_INPUT => ['showitem' => '--palette--;;core, --palette--;;input'],
        \Pixelant\PxaProductManager\Domain\Model\OrderFormField::FIELD_TEXTAREA => ['showitem' => '--palette--;;core, --palette--;;textarea'],
        \Pixelant\PxaProductManager\Domain\Model\OrderFormField::FIELD_SELECTBOX => ['showitem' => '--palette--;;core, --palette--;;selectbox'],
    ],
    'palettes' => [
        'core' => ['showitem' => 'hidden'],
        'input' => ['showitem' => 'name, user_email_field, --linebreak--, label, placeholder, --linebreak--, type, --linebreak--, validation_rules'],
        'textarea' => ['showitem' => 'name, --linebreak--, label, placeholder, --linebreak--, type, --linebreak--, validation_rules'],
        'selectbox' => ['showitem' => 'name, --linebreak--, label, --linebreak--, type, --linebreak--, options, --linebreak--, validation_rules']
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
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
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => $llCore . 'locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_pxaproductmanager_domain_model_orderformfield',
                'foreign_table_where' => 'AND tx_pxaproductmanager_domain_model_orderformfield.pid=###CURRENT_PID### AND tx_pxaproductmanager_domain_model_orderformfield.sys_language_uid IN (-1,0)',
                'default' => 0
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => $llCore . 'locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => $llCore . 'locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => $llCore . 'locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'eval' => 'datetime',
                'renderType' => 'inputDateTime',
                'default' => 0,
            ]
        ],
        'endtime' => [
            'exclude' => true,
            'label' => $llCore . 'locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'eval' => 'datetime',
                'default' => 0,
                'renderType' => 'inputDateTime',
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ]
            ],
        ],
        'name' => [
            'exclude' => 0,
            'label' => $ll . '.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ]
        ],
        'label' => [
            'exclude' => 0,
            'label' => $ll . '.label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ]
        ],
        'type' => [
            'exclude' => 0,
            'onChange' => 'reload',
            'label' => $ll . '.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$ll . '.type.1', \Pixelant\PxaProductManager\Domain\Model\OrderFormField::FIELD_INPUT],
                    [$ll . '.type.2', \Pixelant\PxaProductManager\Domain\Model\OrderFormField::FIELD_TEXTAREA],
                    [$ll . '.type.3', \Pixelant\PxaProductManager\Domain\Model\OrderFormField::FIELD_SELECTBOX]
                ]
            ]
        ],
        'placeholder' => [
            'exclude' => 0,
            'label' => $ll . '.placeholder',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ]
        ],
        'options' => [
            'exclude' => 0,
            'label' => $ll . '.options',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_option',
                'foreign_field' => 'order_field',
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
        'validation_rules' => [
            'exclude' => 0,
            'label' => $ll . '.validation_rules',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'items' => [
                    [
                        $ll . '.validation.required',
                        'required'
                    ],
                    [
                        $ll . '.validation.email',
                        'email'
                    ],
                    [
                        $ll . '.validation.url',
                        'url'
                    ]
                ]
            ]
        ],
        'user_email_field' => [
            'exclude' => true,
            'label' => $ll . '.user_email_field',
            'config' => [
                'type' => 'check',
                'default' => 0
            ],
        ]
    ]
];

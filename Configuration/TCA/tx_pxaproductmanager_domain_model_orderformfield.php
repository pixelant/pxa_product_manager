<?php
defined('TYPO3_MODE') || die('Access denied.');

return (function () {
    $ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_orderformfield';
    $llCore = 'LLL:EXT:core/Resources/Private/Language/';

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
            'hideTable' => true,
            'searchFields' => 'name',
            'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/form-field.svg'
        ],
        'interface' => [
            'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, label, user_email_field, additional_text, type',
        ],
        'types' => [
            \Pixelant\PxaProductManager\Domain\Model\OrderFormField::FIELD_INPUT => ['showitem' => '--palette--;;core, --palette--;;input'],
            \Pixelant\PxaProductManager\Domain\Model\OrderFormField::FIELD_TEXTAREA => ['showitem' => '--palette--;;core, --palette--;;textarea'],
            \Pixelant\PxaProductManager\Domain\Model\OrderFormField::FIELD_SELECTBOX => ['showitem' => '--palette--;;core, --palette--;;selectbox'],
            \Pixelant\PxaProductManager\Domain\Model\OrderFormField::FIELD_CHECKBOX => ['showitem' => '--palette--;;core, --palette--;;checkbox']
        ],
        'palettes' => [
            'core' => ['showitem' => 'hidden, --linebreak--, type'],
            'input' => ['showitem' => 'name, user_email_field, --linebreak--, label, placeholder, --linebreak--, validation_rules'],
            'textarea' => ['showitem' => 'name, --linebreak--, label, placeholder, --linebreak--, validation_rules'],
            'selectbox' => ['showitem' => 'name, --linebreak--, label, --linebreak--, options, --linebreak--, validation_rules'],
            'checkbox' => ['showitem' => 'name, --linebreak--, label, --linebreak--, additional_text, --linebreak--, validation_rules']
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
                    'foreign_table_where' => 'AND tx_pxaproductmanager_domain_model_orderformfield.uid=###REC_FIELD_l10n_parent### AND tx_pxaproductmanager_domain_model_orderformfield.sys_language_uid IN (-1,0)',
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
                    'eval' => 'datetime,int',
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
                    'eval' => 'datetime,int',
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
                    'eval' => 'trim,required,uniqueInPid,alpha,nospace,Pixelant\\PxaProductManager\\Backend\\Evaluation\\LcFirstEvaluation'
                ]
            ],
            'label' => [
                'exclude' => 0,
                'label' => $ll . '.label',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim,required'
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
                        [$ll . '.type.3', \Pixelant\PxaProductManager\Domain\Model\OrderFormField::FIELD_SELECTBOX],
                        [$ll . '.type.4', \Pixelant\PxaProductManager\Domain\Model\OrderFormField::FIELD_CHECKBOX]
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
                'exclude' => 0,
                'label' => $ll . '.user_email_field',
                'config' => [
                    'type' => 'check',
                    'default' => 0
                ],
            ],
            'additional_text' => [
                'exclude' => 0,
                'label' => $ll . '.additional_text',
                'config' => [
                    'type' => 'text',
                    'cols' => 15,
                    'rows' => 5,
                    'enableRichtext' => true
                ]
            ]
        ]
    ];
})();

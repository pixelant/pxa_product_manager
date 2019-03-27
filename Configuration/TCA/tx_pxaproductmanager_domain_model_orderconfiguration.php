<?php

$ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_orderconfiguration';
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
        'searchFields' => 'name',
        'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/form_fields.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, form_fields, admin_emails',
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, enabled_email_to_user, enabled_replace_with_fe_user_fields, admin_emails,
            --div--;' . $ll . '.tabs.form_fields, form_fields,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'
        ],
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
                'foreign_table' => 'tx_pxaproductmanager_domain_model_orderconfiguration',
                'foreign_table_where' => 'AND tx_pxaproductmanager_domain_model_orderconfiguration.pid=###CURRENT_PID### AND tx_pxaproductmanager_domain_model_orderconfiguration.sys_language_uid IN (-1,0)',
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
                'eval' => 'trim,required'
            ]
        ],
        'form_fields' => [
            'exclude' => 0,
            'label' => $ll . '.form_fields',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_orderformfield',
                'foreign_field' => 'order_configuration',
                'foreign_sortby' => 'sorting',
                'maxitems' => 9999,
                'minitems' => 1,
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
        'enabled_email_to_user' => [
            'exclude' => 0,
            'label' => $ll . '.enabled_email_to_user',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => $llCore . 'locallang_core.xlf:labels.enabled'
                    ]
                ]
            ]
        ],
        'enabled_replace_with_fe_user_fields' => [
            'exclude' => 0,
            'label' => $ll . '.enabled_replace_with_fe_user_fields',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => $llCore . 'locallang_core.xlf:labels.enabled'
                    ]
                ]
            ]
        ],
        'admin_emails' => [
            'exclude' => 0,
            'label' => $ll . '.admin_emails',
            'config' => [
                'type' => 'text',
                'cols' => 15,
                'rows' => 5
            ]
        ],
    ]
];

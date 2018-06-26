<?php

$ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_order';

return [
    'ctrl' => [
        'title' => $ll,
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
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
        'searchFields' => 'products',
        #'hideTable' => true,
        'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/cart_tca.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, products, serialized_products_quantity, serialized_order_fields, external_id, fe_user, seen_by_be_users',
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, products, fe_user, seen_by_be_users,
            --div--;Order fields,|order_fields|,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
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
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_pxaproductmanager_domain_model_order',
                'foreign_table_where' => 'AND tx_pxaproductmanager_domain_model_order.pid=###CURRENT_PID### AND tx_pxaproductmanager_domain_model_order.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
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
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
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
        'products' => [
            'exclude' => 0,
            'label' => $ll . '.products',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_product',
                'foreign_table_where' => 'AND tx_pxaproductmanager_domain_model_product.sys_language_uid <= 0',
                'MM' => 'tx_pxaproductmanager_order_product_mm',
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
            ],
        ],
        'seen_by_be_users' => [
            'exclude' => 0,
            'label' => $ll . '.seen_by_be_users',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'be_users',
                'MM' => 'tx_pxaproductmanager_order_seen_by_be_users_mm',
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
            ],
        ],
        'fe_user' => [
            'exclude' => 0,
            'label' => $ll . '.fe_user',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'size' => 1,
                'maxitems' => 1,
                'items' => [
                    [$ll . '.fe_user.none_is_selected', 0]
                ],
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => false
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ]
                ]
            ],
        ],
        'serialized_order_fields' => [
            'exclude' => 1,
            'label' => 'serialized_order_fields',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'serialized_products_quantity' => [
            'exclude' => 1,
            'label' => 'serialized_products_quantity',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'external_id' => [
            'exclude' => 1,
            'label' => 'External id',
            'config' => [
                'type' => 'passthrough'
            ]
        ]
    ],
];

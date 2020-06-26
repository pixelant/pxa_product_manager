<?php

$ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_order';
$llCore = \Pixelant\PxaProductManager\Utility\TCAUtility::getCoreLLPath();

return [
    'ctrl' => [
        'title' => $ll,
        'label' => 'uid',
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
        'searchFields' => 'products, external_id',
        #'hideTable' => true,
        'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/cart_tca.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, complete, products, serialized_products_quantity, serialized_order_fields, external_id, fe_user, checkout_type, price_at_checkout, tax_at_checkout, subscription',
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, complete, products, fe_user, checkout_type, price_at_checkout, tax_at_checkout, external_id, subscription,
            --div--;' . $ll . '.order_fields,|order_fields|,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime',
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
                'foreign_table' => 'tx_pxaproductmanager_domain_model_order',
                'foreign_table_where' => 'AND tx_pxaproductmanager_domain_model_order.pid=###CURRENT_PID### AND tx_pxaproductmanager_domain_model_order.sys_language_uid IN (-1,0)',
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
        'complete' => [
            'exclude' => true,
            'label' => $ll . '.complete',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => $ll . '.complete.yes'
                    ]
                ],
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
        'fe_user' => [
            'exclude' => 0,
            'label' => $ll . '.fe_user',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'size' => 1,
                'maxitems' => 1,
                'default' => 0,
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
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true
            ]
        ],
        'crdate' => [
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'checkout_type' => [
            'exclude' => true,
            'label' => $ll . '.checkout_type',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => true,
                'default' => 'default'
            ],
        ],
        'coupons' => [
            'exclude' => 0,
            'label' => $ll . '.coupons',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_coupon',
                'MM' => 'tx_pxaproductmanager_order_coupon_mm',
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
        'price_at_checkout' => [
            'exclude' => 0,
            'label' => $ll . '.price_at_checkout',
            'config' => [
                'type' => 'input',
                'default' => 0.0,
                'size' => 5,
                'eval' => 'double2'
            ],
        ],
        'tax_at_checkout' => [
            'exclude' => 0,
            'label' => $ll . '.tax_at_checkout',
            'config' => [
                'type' => 'input',
                'default' => 0.0,
                'size' => 5,
                'eval' => 'double2'
            ],
        ],
        'subscription' => [
            'exclude' => 1,
            'label' => $ll . '.subscription',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_subscription',
                'size' => 1,
                'maxitems' => 1,
                'default' => 0,
                'items' => [
                    [$ll . '.subscription.single', 0]
                ],
                'fieldControl' => [
                    'editPopup' => [
                        'disabled' => true
                    ],
                    'addRecord' => [
                        'disabled' => false,
                    ]
                ]
            ],
        ],
    ]
];

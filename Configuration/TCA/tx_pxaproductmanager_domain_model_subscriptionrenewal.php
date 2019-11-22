<?php

$ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_subscriptionrenewal';
$llCore = \Pixelant\PxaProductManager\Utility\TCAUtility::getCoreLLPath();

return [
    'ctrl' => [
        'title' => $ll,
        'label' => 'payment_date',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'delete' => 'deleted',
        'default_sortby' => 'payment_date',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'payment_date, shipment_date',
//        'hideTable' => true,
        'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/subscription_renewal_tca.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'payment_date, payment_next_try, payment_done, payment_attempts_left, shipment_date,
                                    shipment_next_try, shipment_done, shipment_attempts_left'
    ],
    'types' => [
        '1' => [
            'showitem' => 'payment_date, payment_next_try, payment_done, payment_attempts_left, shipment_date,
                            shipment_next_try, shipment_done, shipment_attempts_left'
        ],
    ],
    'columns' => [
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
        'payment_date' => [
            'exclude' => true,
            'label' => $ll . '.payment_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 15,
                'eval' => 'datetime',
                'default' => time(),
//                'readOnly' => 1,
//                'format' => 'date'
            ],
        ],
        'payment_next_try' => [
            'exclude' => true,
            'label' => $ll . '.payment_next_try',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 15,
                'eval' => 'datetime',
                'default' => time(),
//                'readOnly' => 1
            ],
        ],
        'payment_done' => [
            'exclude' => true,
            'label' => $ll . '.payment_done',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => $llCore . 'locallang_core.xlf:labels.enabled'
                    ]
                ],
//                'readOnly' => 1
            ],
        ],
        'payment_attempts_left' => [
            'exclude' => true,
            'label' => $ll . '.payment_attempts_left',
            'config' => [
                'type' => 'input',
                'size' => 30,
//                'readOnly' => 1
            ],
        ],
        'shipment_date' => [
            'exclude' => true,
            'label' => $ll . '.shipment_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 15,
                'eval' => 'datetime',
                'default' => time(),
//                'format' => 'date',
//                'readOnly' => 1
            ],
        ],
        'shipment_next_try' => [
            'exclude' => true,
            'label' => $ll . '.shipment_next_try',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 15,
                'eval' => 'datetime',
                'default' => time(),
//                'readOnly' => 1
            ],
        ],
        'shipment_done' => [
            'exclude' => true,
            'label' => $ll . '.shipment_done',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => $llCore . 'locallang_core.xlf:labels.enabled'
                    ]
                ],
//                'readOnly' => 1
            ],
        ],
        'shipment_attempts_left' => [
            'exclude' => true,
            'label' => $ll . '.payment_attempts_left',
            'config' => [
                'type' => 'input',
                'size' => 30,
//                'readOnly' => 1
            ],
        ],
        'order' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ]
];

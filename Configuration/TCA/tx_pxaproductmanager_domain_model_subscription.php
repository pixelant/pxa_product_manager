<?php

$ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_subscription';
$llCore = \Pixelant\PxaProductManager\Utility\TCAUtility::getCoreLLPath();

return [
    'ctrl' => [
        'title' => $ll,
        'label' => 'renew_date',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'delete' => 'deleted',
        'default_sortby' => 'renew_date',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'renew_date, shipment_date',
//        'hideTable' => true,
        'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/subscription_tca.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'renew_date, next_try, status, last_renew_status, attempts_left, 
                                    serialized_products_quantity, subscription_period, orders'
    ],
    'types' => [
        '1' => [
            'showitem' => 'renew_date, next_try, status, last_renew_status, attempts_left, serialized_products_quantity,
                            subscription_period, orders'
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
        'renew_date' => [
            'exclude' => true,
            'label' => $ll . '.renew_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 15,
                'eval' => 'datetime',
                'default' => time(),
            ],
        ],
        'next_try' => [
            'exclude' => true,
            'label' => $ll . '.next_try',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 15,
                'eval' => 'datetime',
                'default' => time(),
            ],
        ],
        'attempts_left' => [
            'exclude' => true,
            'label' => $ll . '.attempts_left',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ],
        ],
        'last_renew_status' => [
            'exclude' => 0,
            'label' => $ll . '.last_renew_status',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ]
        ],
        'status' => [
            'exclude' => 0,
            'label' => $ll . '.status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$ll . '.status.1', \Pixelant\PxaProductManager\Domain\Model\Subscription::STATUS_ACTIVE],
                    [$ll . '.status.2', \Pixelant\PxaProductManager\Domain\Model\Subscription::STATUS_PAUSED],
                    [$ll . '.status.3', \Pixelant\PxaProductManager\Domain\Model\Subscription::STATUS_CANCELED]
                ]
            ]
        ],
        'orders' => [
            'exclude' => 1,
            'label' => $ll . '.orders',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_order',
                'foreign_field' => 'subscription',
                'foreign_sortby' => 'crdate',
                'appearance' => [
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                ],
            ]
        ],
        'serialized_products_quantity' => [
            'exclude' => 1,
            'label' => $ll . '.serialized_products_quantity',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'subscription_period' => [
            'exclude' => 1,
            'label' => $ll . '.subscription_period',
            'config' => [
                'type' => 'select',
                'items' => [
                    [
                        $ll . '.subscription_period.' .
                                    \Pixelant\PxaProductManager\Domain\Model\Subscription::RECURRING_FOR_WEEK,
                        \Pixelant\PxaProductManager\Domain\Model\Subscription::RECURRING_FOR_WEEK
                    ],
                    [
                        $ll . '.subscription_period.' .
                                    \Pixelant\PxaProductManager\Domain\Model\Subscription::RECURRING_FOR_MONTH,
                        \Pixelant\PxaProductManager\Domain\Model\Subscription::RECURRING_FOR_MONTH
                    ]
                ]
            ]
        ],
    ]
];

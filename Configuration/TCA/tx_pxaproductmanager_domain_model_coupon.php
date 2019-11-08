<?php

$ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_coupon';
$llCore = \Pixelant\PxaProductManager\Utility\TCAUtility::getCoreLLPath();

return [
    'ctrl' => [
        'title' => $ll,
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'name,code',
        #'hideTable' => true,
        'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/coupon_tca.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, name, code, type, value, usage_limit, cost_limit, usage_count, total_cost'
    ],
    'types' => [
        '1' => [
            'showitem' => 'hidden, name, code, type, value, usage_limit, cost_limit, usage_count, total_cost,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'
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
        'name' => [
            'exclude' => 0,
            'label' => $ll . '.name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'code' => [
            'exclude' => 0,
            'label' => $ll . '.code',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'upper,alphanum_x,nospace,unique,trim,required'
            ],
        ],
        'type' => [
            'exclude' => 0,
            'label' => $ll . '.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$ll . '.type.' . \Pixelant\PxaProductManager\Domain\Model\Coupon::TYPE_CASH_REBATE, \Pixelant\PxaProductManager\Domain\Model\Coupon::TYPE_CASH_REBATE],
                    [$ll . '.type.' . \Pixelant\PxaProductManager\Domain\Model\Coupon::TYPE_PERCENTAGE_REBATE, \Pixelant\PxaProductManager\Domain\Model\Coupon::TYPE_PERCENTAGE_REBATE]
                ],
                'eval' => 'required'
            ],
        ],
        'value' => [
            'exclude' => 0,
            'label' => $ll . '.value',
            'config' => [
                'type' => 'input',
                'size' => 5,
                'eval' => 'double2,required'
            ],
        ],
        'usage_limit' => [
            'exclude' => 1,
            'label' => $ll . '.usage_limit',
            'config' => [
                'type' => 'input',
                'default' => 0,
                'size' => 5,
                'eval' => 'int'
            ],
        ],
        'cost_limit' => [
            'exclude' => 0,
            'label' => $ll . '.cost_limit',
            'config' => [
                'type' => 'input',
                'default' => 0.0,
                'size' => 5,
                'eval' => 'double2'
            ],
        ],
        'usage_count' => [
            'exclude' => 1,
            'label' => $ll . '.usage_limit',
            'config' => [
                'type' => 'input',
                'default' => 0,
                'size' => 10,
                'eval' => 'int'
            ],
        ],
        'total_cost' => [
            'exclude' => 1,
            'label' => $ll . '.total_cost',
            'config' => [
                'type' => 'input',
                'default' => 0,
                'size' => 10,
                'eval' => 'int'
            ],
        ],
    ]
];

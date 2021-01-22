<?php

defined('TYPO3_MODE') || die('Access denied.');

return (function () {
    $ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_attributeset';

    return [
        'ctrl' => [
            'title' => $ll,
            'label' => 'name',
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'cruser_id' => 'cruser_id',
            'dividers2tabs' => true,
            'sortby' => 'sorting',
            'origUid' => 't3_origuid',
            'delete' => 'deleted',
            'enablecolumns' => [
                'disabled' => 'hidden',
            ],
            'searchFields' => 'name,attributes,',
            'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/layers.svg',
        ],
        'interface' => [
            'showRecordFieldList' => 'hidden, name, layout, attributes, product_types',
        ],
        'types' => [
            '1' => ['showitem' => 'name, layout, attributes, product_types, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, hidden'],
        ],
        'palettes' => [
            '1' => ['showitem' => ''],
        ],
        'columns' => [
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
            'name' => [
                'label' => $ll . '.name',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim,required',
                ],
            ],
            'attributes' => [
                'label' => $ll . '.attributes',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectMultipleSideBySide',
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_attribute',
                    'foreign_table_where' => 'AND tx_pxaproductmanager_domain_model_attribute.pid = ###CURRENT_PID###' .
                        ' AND tx_pxaproductmanager_domain_model_attribute.sys_language_uid <= 0',
                    'MM' => 'tx_pxaproductmanager_attributeset_record_mm',
                    'MM_match_fields' => [
                        'tablenames' => 'tx_pxaproductmanager_domain_model_attribute',
                        'fieldname' => 'attributes',
                    ],
                    'size' => 10,
                    'autoSizeMax' => 30,
                    'maxitems' => 9999,
                    'multiple' => 0,
                    'fieldControl' => [
                        'editPopup' => [
                            'disabled' => false,
                        ],
                        'addRecord' => [
                            'disabled' => false,
                        ],
                    ],
                ],
            ],
            'layout' => [
                'exclude' => true,
                'label' => $ll . '.layout',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        [$ll . '.layout.default', 'Default'],
                    ],
                    'default' => 'Default',
                ],
            ],
            'product_types' => [
                'label' => $ll . '.product_types',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectMultipleSideBySide',
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_producttype',
                    'MM' => 'tx_pxaproductmanager_attributeset_record_mm',
                    'MM_match_fields' => [
                        'tablenames' => 'tx_pxaproductmanager_domain_model_producttype',
                        'fieldname' => 'product_type',
                    ],
                    'size' => 10,
                    'autoSizeMax' => 30,
                    'maxitems' => 9999,
                    'multiple' => 0,
                    'fieldControl' => [
                        'editPopup' => [
                            'disabled' => false,
                        ],
                        'addRecord' => [
                            'disabled' => false,
                        ],
                    ],
                ],
            ],
        ],
    ];
})();

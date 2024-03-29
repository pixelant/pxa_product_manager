<?php

defined('TYPO3_MODE') || die('Access denied.');

return (function () {
    $ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:';

    return [
        'ctrl' => [
            'title' => $ll . 'tx_pxaproductmanager_domain_model_producttype',
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
            ],
            'searchFields' => 'name',
            'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/filter.svg',
        ],
        'types' => [
            '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, attribute_sets, template_layout, inherit_fields, '],
        ],
        'palettes' => [
            '1' => ['showitem' => ''],
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
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => 'tx_pxaproductmanager_domain_model_producttype',
                    'size' => 1,
                    'maxitems' => 1,
                    'minitems' => 0,
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
            'name' => [
                'exclude' => true,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_producttype.name',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim,required',
                ],
            ],
            'attribute_sets' => [
                'exclude' => true,
                'onChange' => 'reload',
                'label' => $ll . 'tx_pxaproductmanager_domain_model_producttype.attribute_sets',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectMultipleSideBySide',
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_attributeset',
                    'foreign_table_where' => \Pixelant\PxaProductManager\Utility\TcaUtility::getAttributesSetsForeignTableWherePid() .
                        ' ORDER BY tx_pxaproductmanager_domain_model_attributeset.sorting',
                    'MM' => 'tx_pxaproductmanager_attributeset_record_mm',
                    'MM_match_fields' => [
                        'tablenames' => 'tx_pxaproductmanager_domain_model_producttype',
                        'fieldname' => 'product_type',
                    ],
                    'MM_opposite_field' => 'product_types',
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
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'template_layout' => [
                'exclude' => true,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_producttype.attribute_template_layout',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
            'inherit_fields' => [
                'label' => $ll . 'tx_pxaproductmanager_domain_model_producttype.inherit_fields',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectCheckBox',
                    'itemsProcFunc' => \Pixelant\PxaProductManager\Hook\ItemsProcFunc\ProductItemsProcFunc::class . '->getProductFields',
                    'itemsProcConfig' => [
                        'exclude' => 'attributes_values',
                    ],
                    'size' => 10,
                    'autoSizeMax' => 30,
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                ],
            ],
        ],
    ];
})();

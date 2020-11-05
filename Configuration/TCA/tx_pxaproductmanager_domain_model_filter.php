<?php

defined('TYPO3_MODE') || die('Access denied.');

return (function () {
    $ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:';

    return [
        'ctrl' => [
            'title' => $ll . 'tx_pxaproductmanager_domain_model_filter',
            'label' => 'name',
            'label_alt' => 'label',
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

            'type' => 'type',

            'searchFields' => 'name,category,attribute',
            'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/filter.svg',
        ],
        'interface' => [
            'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, type, name, label, category, attribute, conjunction',
        ],
        'types' => [
            '1' => ['showitem' => '--palette--;;core, --palette--;;common, --palette--;;categories,'],
            '2' => ['showitem' => '--palette--;;core, --palette--;;common, --palette--;;attributes,'],
            '3' => ['showitem' => '--palette--;;core, --palette--;;common, --palette--;;attributes,'],
        ],
        'palettes' => [
            'core' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, --linebreak--, hidden'],
            'common' => ['showitem' => 'type, --linebreak--, name, label'],
            'categories' => ['showitem' => 'conjunction, --linebreak--, category'],
            'attributes' => ['showitem' => 'conjunction, --linebreak--, attribute'],
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
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        ['', 0],
                    ],
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_filter',
                    'foreign_table_where' => 'AND tx_pxaproductmanager_domain_model_filter.pid=###CURRENT_PID### AND tx_pxaproductmanager_domain_model_filter.sys_language_uid IN (-1,0)',
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

            'type' => [
                'exclude' => true,
                'onChange' => 'reload',
                'label' => $ll . 'tx_pxaproductmanager_domain_model_filter.type',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        ['Categories', \Pixelant\PxaProductManager\Domain\Model\Filter::TYPE_CATEGORIES],
                        ['Attribute', \Pixelant\PxaProductManager\Domain\Model\Filter::TYPE_ATTRIBUTES],
                        ['Attribute min-max (if applicable, require only numeric attribute values)', \Pixelant\PxaProductManager\Domain\Model\Filter::TYPE_ATTRIBUTES_MINMAX],
                    ],
                    'size' => 1,
                    'maxitems' => 1,
                    'eval' => '',
                ],
            ],
            'name' => [
                'exclude' => true,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_filter.name',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim,required',
                ],
            ],
            'label' => [
                'exclude' => false,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_filter.label',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim',
                ],
            ],
            'category' => [
                'exclude' => true,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_filter.category',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectTree',
                    'treeConfig' => [
                        'parentField' => 'parent',
                        'appearance' => [
                            'showHeader' => true,
                            'expandAll' => true,
                            'maxLevels' => 99,
                        ],
                    ],
                    'foreign_table' => 'sys_category',
                    'foreign_table_where' => ' AND (sys_category.sys_language_uid = 0 OR sys_category.l10n_parent = 0) ORDER BY sys_category.sorting',
                    'size' => 20,
                    'minitems' => 1,
                    'maxitems' => 1,
                ],
            ],
            'attribute' => [
                'exclude' => true,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_filter.attribute',
                'config' => [
                    'type' => 'select',
                    'disableNoMatchingValueElement' => true,
                    'renderType' => 'selectSingle',
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_attribute',
                    'foreign_table_where' => ' AND tx_pxaproductmanager_domain_model_attribute.type IN ('
                        . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_DROPDOWN . ','
                        . \Pixelant\PxaProductManager\Domain\Model\Attribute::ATTRIBUTE_TYPE_MULTISELECT . ')' .
                        ' AND (tx_pxaproductmanager_domain_model_attribute.sys_language_uid = 0 OR tx_pxaproductmanager_domain_model_attribute.l10n_parent = 0) ORDER BY tx_pxaproductmanager_domain_model_attribute.sorting',
                    'minitems' => 1,
                    'maxitems' => 1,
                ],
            ],
            'conjunction' => [
                // hide for range filter
                'displayCond' => 'FIELD:type:!=:3',
                'exclude' => true,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_filter.conjunction',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'default' => 'and',
                    'items' => [
                        ['And', \Pixelant\PxaProductManager\Domain\Model\Filter::CONJUNCTION_AND],
                        ['Or', \Pixelant\PxaProductManager\Domain\Model\Filter::CONJUNCTION_OR],
                    ],
                ],
            ],
        ],
    ];
})();

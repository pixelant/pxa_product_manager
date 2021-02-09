<?php

defined('TYPO3_MODE') || die('Access denied.');

$attributes = \Pixelant\PxaProductManager\Utility\AttributeUtility::findAllAttributes();

$types = [];

foreach ($attributes as $attribute) {
    $types[(string)$attribute['uid']] = [
        'showitem' => 'value',
        'columnsOverrides' => [
            'value' => \Pixelant\PxaProductManager\Attributes\ConfigurationProvider\ConfigurationProviderFactory::create((int)$attribute['uid'])->get(),
        ],
    ];
}

return [
    'ctrl' => [
        'title' => 'Attribute values',
        'label' => 'value',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'type' => 'attribute',
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'searchFields' => 'value,attribute,',
        'hideTable' => true,
        'rootLevel' => -1,
    ],
    'types' => $types,
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
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
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_pxaproductmanager_domain_model_attributevalue',
                'foreign_table_where' => 'AND tx_pxaproductmanager_domain_model_attributevalue.pid=###CURRENT_PID### AND tx_pxaproductmanager_domain_model_attributevalue.sys_language_uid IN (-1,0)',
                'default' => 0,
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'value' => [
            'label' => 'Value',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'attribute' => [
            'label' => 'Attribute',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_attribute',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'product' => [
            'label' => 'product',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_product',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
    ],
];

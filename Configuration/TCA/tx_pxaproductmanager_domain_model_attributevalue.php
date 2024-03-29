<?php

defined('TYPO3_MODE') || die('Access denied.');

try {
    $attributes = \Pixelant\PxaProductManager\Utility\AttributeUtility::findAllAttributes();
} catch (\Doctrine\DBAL\Exception\TableNotFoundException $th) {
    // catch TableNotFoundException to avoid exception when table isn't created yet.
    $attributes = [];
}

$types = [
    '0' => [
        'showitem' => 'value',
    ],
];

foreach ($attributes as $attribute) {
    if ($attribute['uid'] === 0) {
        continue;
    }

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
        'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/tag.svg',
        'security' => [
            'ignoreRootLevelRestriction' => true,
            'ignoreWebMountRestriction' => true,
        ],
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
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_pxaproductmanager_domain_model_attributevalue',
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
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_pxaproductmanager_domain_model_attribute',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
            ],
        ],
        'product' => [
            'label' => 'product',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_pxaproductmanager_domain_model_product',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
            ],
        ],
    ],
];

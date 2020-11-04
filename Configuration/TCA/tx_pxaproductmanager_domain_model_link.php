<?php
defined('TYPO3_MODE') || die('Access denied.');

return (function () {
    $ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:';

    return [
        'ctrl' => [
            'title' => $ll . 'tx_pxaproductmanager_domain_model_link',
            'label' => 'name',
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'cruser_id' => 'cruser_id',
            'dividers2tabs' => true,
            'sortby' => 'sorting',
            'origUid' => 't3_origuid',
            'languageField' => 'sys_language_uid',
            'transOrigPointerField' => 'l10n_parent',
            'transOrigDiffSourceField' => 'l10n_diffsource',
            'delete' => 'deleted',
            'enablecolumns' => [
                'disabled' => 'hidden',
            ],
            'searchFields' => 'name,link',
            'hideTable' => 1,
            'iconfile' => 'EXT:pxa_product_manager/Resources/Public/Icons/Svg/link.svg',
        ],
        'interface' => [
            'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, link, description',
        ],
        'types' => [
            '1' => ['showitem' => 'hidden, name, link, description'],
        ],
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
                    'foreign_table' => 'tx_pxaproductmanager_domain_model_link',
                    'foreign_table_where' => 'AND tx_pxaproductmanager_domain_model_link.pid=###CURRENT_PID###' .
                        ' AND tx_pxaproductmanager_domain_model_link.sys_language_uid IN (-1,0)',
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
                'exclude' => 0,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_link.name',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim,required',
                ],
            ],
            'link' => [
                'exclude' => 0,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_link.link',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'max' => 256,
                    'eval' => 'trim,required',
                    'renderType' => 'inputLink',
                    'softref' => 'typolink',
                ],
            ],
            'description' => [
                'exclude' => 0,
                'label' => $ll . 'tx_pxaproductmanager_domain_model_link.description',
                'config' => [
                    'type' => 'text',
                    'cols' => 60,
                    'rows' => 5,
                    'eval' => 'trim',
                ],
            ],
            'product' => [
                'config' => [
                    'type' => 'passthrough',
                ],
            ],
        ],
    ];
})();

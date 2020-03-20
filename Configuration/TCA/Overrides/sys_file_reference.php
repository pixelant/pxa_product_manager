<?php
defined('TYPO3_MODE') || die;

(function () {
    $ll = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:';

    $columns = [
        'pxapm_type' => [
            'label' => $ll . 'sys_file_reference.pxapm_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$ll . 'sys_file_reference.pxapm_type.0', 0],
                    [$ll . 'sys_file_reference.pxapm_type.1', 1],
                    [$ll . 'sys_file_reference.pxapm_type.2', 2],
                ],
            ],
        ],
    ];


    $columnsForAttribute = [
        'pxa_attribute' => [
            'label' => $ll . 'sys_file_reference.pxa_attribute',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_pxaproductmanager_domain_model_attribute',
                'items' => [],
            ],
        ],
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'sys_file_reference',
        $columns
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'sys_file_reference',
        $columnsForAttribute
    );

    // add special product manager palette
    $GLOBALS['TCA']['sys_file_reference']['palettes']['pxaProductManagerPalette'] = [
        'showitem' => 'pxapm_type',
        'canNotCollapse' => true
    ];

    $GLOBALS['TCA']['sys_file_reference']['palettes']['pxaProductManagerAttributePalette'] = [
        'showitem' => 'pxa_attribute',
        'canNotCollapse' => true
    ];
})();

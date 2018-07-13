<?php
defined('TYPO3_MODE') || die;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    'pxa_product_manager',
    'tx_pxaproductmanager_domain_model_product',
    // optional: in case the field would need a different name as "categories".
    // The field is mandatory for TCEmain to work internally.
    'categories',
    // optional: add TCA options which controls how the field is displayed. e.g position and name of the category tree.
    [
        'fieldConfiguration' => [
            'foreign_table_where' => \Pixelant\PxaProductManager\Utility\TCAUtility::getCategoriesTCAWhereClause()
                . 'AND sys_category.sys_language_uid IN (-1, 0)'
        ]
    ]
);

$GLOBALS['TCA']['tx_pxaproductmanager_domain_model_product']['columns']['categories']['onChange'] = 'reload';

if (!\Pixelant\PxaProductManager\Utility\MainUtility::isPricingEnabled()) {
    $columns = &$GLOBALS['TCA']['tx_pxaproductmanager_domain_model_product']['columns'];
    $columns['price']['config']['readOnly'] = true;
    $columns['tax_rate']['config']['readOnly'] = true;
    // @codingStandardsIgnoreStart
    $columns['price']['label'] = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_product.price_disabled';
    $columns['tax_rate']['label'] = 'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_db.xlf:tx_pxaproductmanager_domain_model_product.tax_rate.disabled';
    // @codingStandardsIgnoreEnd
}

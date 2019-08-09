<?php
defined('TYPO3_MODE') || die;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'pxa_product_manager',
    'Configuration/TypoScript',
    'Products Manager'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'pxa_product_manager',
    'Configuration/TypoScript/Solr',
    'Products Manager: Solr Configuration'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'pxa_product_manager',
    'Configuration/TypoScript/Sitemap',
    'Products Manager: Xml Sitemap'
);

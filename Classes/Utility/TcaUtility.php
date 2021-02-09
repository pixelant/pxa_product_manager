<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 *
 *  Copyright notice
 *
 *  (c) 2016 Pavlo Zaporozkyi <pavlo@pixelant.se>, Pixelant
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * Class TCAUtility.
 */
class TcaUtility
{
    /**
     * Table where for accessories.
     *
     * @return string
     */
    public static function getAccessoriesForeignTableWherePid(): string
    {
        return self::getDynamicForeignTableWhere(
            'accessoriesRestriction',
            'tx_pxaproductmanager_domain_model_product'
        );
    }

    /**
     * Table where for related-products.
     *
     * @return string
     */
    public static function getRelatedProductsForeignTableWherePid(): string
    {
        return self::getDynamicForeignTableWhere(
            'relatedProductsRestriction',
            'tx_pxaproductmanager_domain_model_product'
        );
    }

    /**
     * Table where for sub-products.
     *
     * @return string
     */
    public static function getSubProductsForeignTableWherePid(): string
    {
        return self::getDynamicForeignTableWhere(
            'subProductsRestriction',
            'tx_pxaproductmanager_domain_model_product'
        );
    }

    /**
     * Table where for attributes sets.
     *
     * @return string
     */
    public static function getAttributesSetsForeignTableWherePid(): string
    {
        return self::getDynamicForeignTableWhere(
            'attributesSetsRestriction',
            'tx_pxaproductmanager_domain_model_attributeset'
        );
    }

    /**
     * TCA where clause for categories.
     *
     * @return string
     */
    public static function getCategoriesTCAWhereClause(): string
    {
        return self::getDynamicForeignTableWhere(
            'categoriesRestriction',
            'sys_category'
        );
    }

    /**
     * Returns TCA configuration for a field with type-related overrides
     *
     * @param string $table
     * @param string $field
     * @param array $row
     * @return mixed
     */
    public static function getTcaFieldConfigurationAndRespectColumnsOverrides(string $table, string $field, array $row)
    {
        $tcaFieldConf = $GLOBALS['TCA'][$table]['columns'][$field]['config'];
        $recordType = BackendUtility::getTCAtypeValue($table, $row);
        $columnsOverridesConfigOfField = $GLOBALS['TCA'][$table]['types'][$recordType]['columnsOverrides'][$field]['config'] ?? null;

        if ($columnsOverridesConfigOfField) {
            ArrayUtility::mergeRecursiveWithOverrule($tcaFieldConf, $columnsOverridesConfigOfField);
        }

        return $tcaFieldConf;
    }

    /**
     * Generate dynamic foreign table where.
     *
     * @param $setting
     * @param $table
     * @return string
     */
    protected static function getDynamicForeignTableWhere(string $setting, string $table): string
    {
        // we will use current_pid as default to keep backward compatibility
        $foreignTableWhere = 'AND ' . $table . '.pid = ###CURRENT_PID###';

        // Check and override by typoscript setting
        $restrictionSetting = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('pxa_product_manager', $setting);
        if ($restrictionSetting) {
            switch ($restrictionSetting) {
                case 'current_pid':
                    $foreignTableWhere = ' AND ' . $table . '.pid=###CURRENT_PID### ';

                    break;
                case 'siteroot':
                    $foreignTableWhere = ' AND ' . $table . '.pid IN (###SITEROOT###) ';

                    break;
                case 'page_tsconfig':
                    $foreignTableWhere = ' AND ' . $table . '.pid IN (###PAGE_TSCONFIG_IDLIST###) ';

                    break;
                case 'none':
                    $foreignTableWhere = '';

                    break;
            }
        }

        return $foreignTableWhere;
    }
}

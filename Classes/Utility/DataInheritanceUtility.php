<?php

declare(strict_types=1);


namespace Pixelant\PxaProductManager\Utility;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Convenience methods for handling inheritance of product properties and parent/child relations
 */
class DataInheritanceUtility
{
    protected static array $productTypeInheritedFields = [];

    /**
     * Get an array of field names that are specified as inherited from parent products for this product type
     *
     * @param int $productType ProductType UID
     * @return array
     */
    public static function getInheritedFieldsForProductType(int $productType): array
    {
        if ($productType === 0) {
            return [];
        }

        if (isset(self::$productTypeInheritedFields[$productType])) {
            return self::$productTypeInheritedFields[$productType];
        }

        $productTypeRecord = BackendUtility::getRecord(
            'tx_pxaproductmanager_domain_model_producttype',
            $productType,
            'inherit_fields'
        );

        self::$productTypeInheritedFields[$productType] =
            GeneralUtility::trimExplode(',', $productTypeRecord['inherit_fields'], true);

        return self::$productTypeInheritedFields[$productType];
    }
}

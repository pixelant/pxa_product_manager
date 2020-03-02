<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

use Pixelant\PxaProductManager\Domain\Model\Attribute;

/**
 * @package Pixelant\PxaProductManager\Utility
 */
class AttributeTcaNamingUtility
{
    const FAL_DB_FIELD = 'attributes_files';
    const TCA_PREFIX = 'tx_pxaproductmanager_attribute_';
    const TCA_FAL_PREFIX = 'tx_pxaproductmanager_attribute_fal_';

    /**
     * Get name of TCA form field
     *
     * @param Attribute $attribute
     * @return string
     */
    public static function translateToTcaFieldName(Attribute $attribute): string
    {
        return sprintf(
            '%s%d',
            $attribute->isFalType() ? self::TCA_FAL_PREFIX : self::TCA_PREFIX,
            $attribute->getUid()
        );
    }

    /**
     * Check if given field name is attribute field name
     *
     * @param string $field
     * @return bool
     */
    public static function isAttributeFieldName(string $field): bool
    {
        return strpos($field, self::TCA_PREFIX) === 0;
    }

    /**
     * Check if given field is fal field
     *
     * @param string $field
     * @return bool
     */
    public static function isFileAttributeFieldName(string $field): bool
    {
        return strpos($field, self::TCA_FAL_PREFIX) === 0;
    }

    /**
     * Get ID of attribute from it's TCA name
     *
     * @param string $field
     * @return int
     */
    public static function extractIdFromFieldName(string $field): int
    {
        list($id) = explode('_', strrev($field), 2);

        return (int)strrev($id);
    }
}

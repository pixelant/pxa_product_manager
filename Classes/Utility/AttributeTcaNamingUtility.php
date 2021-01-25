<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

use Pixelant\PxaProductManager\Domain\Model\Attribute;

class AttributeTcaNamingUtility
{
    public const FAL_DB_FIELD = 'attributes_files';
    public const TCA_PREFIX = 'tx_pxaproductmanager_attribute_';
    public const TCA_FAL_PREFIX = 'tx_pxaproductmanager_attribute_fal_';

    /**
     * Get name of TCA form field.
     *
     * @param Attribute $attribute
     * @return string
     */
    public static function translateToTcaFieldName(Attribute $attribute): string
    {
        return self::translateUidAndTypeToFieldName($attribute->getUid(), $attribute->getType());
    }

    /**
     * Get name of TCA form field.
     *
     * @param int $uid The attribute UID
     * @param int $type Attribute::ATTRIBUTE_TYPE_*
     * @return string
     */
    public static function translateUidAndTypeToFieldName(int $uid, int $type)
    {
        $prefix = self::TCA_PREFIX;

        if ($type === Attribute::ATTRIBUTE_TYPE_FILE || $type === Attribute::ATTRIBUTE_TYPE_IMAGE) {
            $prefix = self::TCA_FAL_PREFIX;
        }

        return sprintf(
            '%s%d',
            $prefix,
            $uid
        );
    }

    /**
     * Check if given field name is attribute field name.
     *
     * @param string $field
     * @return bool
     */
    public static function isAttributeFieldName(string $field): bool
    {
        return strpos($field, self::TCA_PREFIX) === 0;
    }

    /**
     * Check if given field is fal field.
     *
     * @param string $field
     * @return bool
     */
    public static function isFileAttributeFieldName(string $field): bool
    {
        return strpos($field, self::TCA_FAL_PREFIX) === 0;
    }

    /**
     * Get ID of attribute from it's TCA name.
     *
     * @param string $field
     * @return int
     */
    public static function extractIdFromFieldName(string $field): int
    {
        [$id] = explode('_', strrev($field), 2);

        return (int)strrev($id);
    }
}

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
    const ATTRIBUTE_PREFIX = 'tx_pxaproductmanager_attribute_';

    /**
     * Get name of TCA form field
     *
     * @param Attribute $attribute
     * @return string
     */
    public static function translateAttributeToTcaFieldName(Attribute $attribute): string
    {
        return static::ATTRIBUTE_PREFIX . $attribute->getUid();
    }
}

<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Adapter\Attributes;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package Pixelant\PxaProductManager\Domain\Service
 */
class AdapterFactory
{
    /**
     * Factory method create adapter depend on attribute
     *
     * @param Attribute $attribute
     * @return AdapterInterface
     */
    public static function factory(Attribute $attribute): AdapterInterface
    {
        switch (true) {
            case $attribute->isFalType():
                $adapter = FalAdapter::class;
                break;
            case $attribute->isSelectBoxType():
                break;
            default:
                $adapter = GeneralAdapter::class;
        }

        return GeneralUtility::makeInstance($adapter);
    }
}

<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic;

use Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic\Qom\AttributesRange;
use TYPO3\CMS\Extbase\Persistence\Generic\Query as ExtbaseQuery;

/**
 * Class Query
 * @package Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic
 */
class Query extends ExtbaseQuery
{
    /**
     * Match products by range filter
     *
     * @param string $propertyName
     * @param int|null $minValue
     * @param int|null $maxValue
     * @return object|AttributesRange
     */
    public function attributesRange(string $propertyName, int $minValue = null, int $maxValue = null)
    {
        return $this->objectManager->get(
            AttributesRange::class,
            $this->qomFactory->propertyValue($propertyName, $this->getSelectorName()),
            $minValue,
            $maxValue
        );
    }
}

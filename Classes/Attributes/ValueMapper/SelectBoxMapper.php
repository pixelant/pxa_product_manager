<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Option;
use Pixelant\PxaProductManager\Domain\Model\Product;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Set attribute options as values.
 */
class SelectBoxMapper extends AbstractMapper
{
    /**
     * {@inheritdoc}
     */
    public function map(Product $product, AttributeValue $attributeValue): void
    {
        if ($attributeValue) {
            $selectedOptions = array_filter(
                $attributeValue->getAttribute()->getOptions()->toArray(),
                function (Option $option) use ($attributeValue) {
                    return GeneralUtility::inList($attributeValue->getValue(), $option->getUid());
                }
            );
            $attributeValue->setArrayValue(array_values($selectedOptions));
        }
    }
}

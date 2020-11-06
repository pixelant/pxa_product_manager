<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
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
    public function map(Product $product, Attribute $attribute): void
    {
        $attributeValue = $this->searchAttributeValue($product, $attribute);
        if ($attributeValue) {
            $selectedOptions = array_filter(
                $attribute->getOptions()->toArray(),
                function (Option $option) use ($attributeValue) {
                    return GeneralUtility::inList($attributeValue->getValue(), $option->getUid());
                }
            );

            $attribute->setArrayValue(array_values($selectedOptions));
        }
    }
}

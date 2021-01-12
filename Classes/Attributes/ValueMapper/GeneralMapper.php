<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * General mapper for string values.
 */
class GeneralMapper extends AbstractMapper
{
    /**
     * {@inheritdoc}
     */
    public function map(Product $product, AttributeValue $attributeValue): void
    {
        if ($attributeValue) {
            $attributeValue->setStringValue($attributeValue->getValue());
        }
    }
}

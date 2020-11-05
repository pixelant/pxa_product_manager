<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * General mapper for string values.
 */
class GeneralMapper extends AbstractMapper
{
    /**
     * {@inheritdoc}
     */
    public function map(Product $product, Attribute $attribute): void
    {
        $attributeValue = $this->searchAttributeValue($product, $attribute);
        if ($attributeValue) {
            $attribute->setStringValue($attributeValue->getValue());
        }
    }
}

<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * Set files for attribute.
 */
class FalMapper extends AbstractMapper
{
    /**
     * {@inheritdoc}
     */
    public function map(Product $product, AttributeValue $attributeValue): void
    {
        $attributeValue->setArrayValue(
            $this
                ->collection($product->getAttributesFiles())
                ->searchByProperty('attribute', $attributeValue->getAttribute()->getUid())
                ->toArray()
        );
    }
}

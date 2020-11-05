<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * Set files for attribute.
 */
class FalMapper extends AbstractMapper
{
    /**
     * {@inheritdoc}
     */
    public function map(Product $product, Attribute $attribute): void
    {
        $attribute->setArrayValue(
            $this
                ->collection($product->getAttributesFiles())
                ->searchByProperty('attribute', $attribute->getUid())
                ->toArray()
        );
    }
}

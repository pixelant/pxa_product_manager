<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * Set files for attribute
 */
class FalMapper extends AbstractMapper
{
    /**
     * @inheritDoc
     */
    public function map(Product $product, Attribute $attribute): void
    {
        $attribute->setValue(
            $this
                ->collection($product->getAttributesFiles())
                ->searchByProperty('attribute', $attribute->getUid())
                ->toArray()
        );
    }
}

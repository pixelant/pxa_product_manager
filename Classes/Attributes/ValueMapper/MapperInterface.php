<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 */
interface MapperInterface
{
    /**
     * Set attribute value.
     * It should fine corresponding attribute value entity,
     * read value and set in given attribute value property
     *
     * @param Product $product
     * @param Attribute $attribute
     */
    public function map(Product $product, Attribute $attribute): void;
}

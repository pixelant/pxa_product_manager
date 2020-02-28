<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Adapter\Attributes;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 */
interface AdapterInterface
{
    /**
     * Adapt attribute value.
     * It should fine corresponding attribute value entity,
     * read DB value and set in given attribute value property
     *
     * @param Product $product
     * @param Attribute $attribute
     */
    public function adapt(Product $product, Attribute $attribute): void;
}

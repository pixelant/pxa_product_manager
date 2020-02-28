<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Adapter\Attributes;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * @package Pixelant\PxaProductManager\Domain\Adapter\Attributes
 */
class FalAdapter extends AbstractAdapter
{

    /**
     * @inheritDoc
     */
    public function adapt(Product $product, Attribute $attribute): void
    {
        $attribute->setValue(
            $this->collection($product->getAttributesFiles())->searchByProperty('attribute', $attribute->getUid())->toArray()
        );
    }
}

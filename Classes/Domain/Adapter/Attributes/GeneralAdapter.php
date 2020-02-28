<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Adapter\Attributes;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * @package Pixelant\PxaProductManager\Domain\Service
 */
class GeneralAdapter extends AbstractAdapter
{
    /**
     * @inheritDoc
     */
    public function adapt(Product $product, Attribute $attribute): void
    {
        if ($attributeValue = $this->searchAttributeValue($product, $attribute)) {
            $attribute->setValue($attributeValue->getValue());
        }
    }
}

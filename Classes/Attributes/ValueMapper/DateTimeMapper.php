<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * @package Pixelant\PxaProductManager\Attributes\ValueMapper
 */
class DateTimeMapper extends AbstractMapper
{
    /**
     * @inheritDoc
     */
    public function map(Product $product, Attribute $attribute): void
    {
        if ($attributeValue = $this->searchAttributeValue($product, $attribute)) {
            try {
                $value = new \DateTime($attributeValue->getValue());
            } catch (\Exception $exception) {
                $value = null;
            }
            $attribute->setValue($value);
        }
    }
}

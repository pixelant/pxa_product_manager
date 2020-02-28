<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Service;

use Pixelant\PxaProductManager\Domain\Adapter\Attributes\AdapterFactory;
use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * Init values for product attribute
 *
 * @package Pixelant\PxaProductManager\Domain\Service
 */
class AttributesValuesMapper
{
    use CanCreateCollection;

    /**
     * Take product, fill all attributes with values and return processed attributes
     *
     * @param Product $product
     * @return array
     */
    public function map(Product $product): array
    {
        $attributes = $this->collection($product->getAllAttributesSets())
            ->pluck('attributes')
            ->shiftLevel()
            ->toArray();

        foreach ($attributes as $attribute) {
            AdapterFactory::factory($attribute)->adapt($product, $attribute);
        }

        return $attributes;
    }
}

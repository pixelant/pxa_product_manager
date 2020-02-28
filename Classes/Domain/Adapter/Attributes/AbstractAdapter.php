<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Adapter\Attributes;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * @package Pixelant\PxaProductManager\Domain\Service
 */
abstract class AbstractAdapter implements AdapterInterface
{
    use CanCreateCollection;

    /**
     * Search for attribute value in product values
     *
     * @param Product $product
     * @param Attribute $attribute
     * @return AttributeValue|null
     */
    protected function searchAttributeValue(Product $product, Attribute $attribute): ?AttributeValue
    {
        return $this->collection($product->getAttributeValues())
            ->searchOneByProperty(
                'attribute',
                $attribute->getUid(),
                fn(Attribute $collectionAttribute) => $collectionAttribute->getUid()
            );
    }
}

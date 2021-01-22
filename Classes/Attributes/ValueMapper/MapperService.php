<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * Init values for product attribute.
 */
class MapperService implements MapperServiceInterface
{
    use CanCreateCollection;

    /**
     * @var MapperFactory
     */
    protected MapperFactory $factory;

    /**
     * @param MapperFactory $factory
     */
    public function __construct(MapperFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Take product, fill all attributes with values and return attributes sets.
     *
     * @param Product $product
     * @return array
     */
    public function map(Product $product): array
    {
        $attributeValues = $product->getAttributesValuesWithValidAttributes();

        $this->process($product, $attributeValues);

        return $attributeValues;
    }

    /**
     * Process all attributes.
     *
     * @param Product $product
     * @param array $attributeValues
     */
    protected function process(Product $product, array $attributeValues): void
    {
        foreach ($attributeValues as $attributeValue) {
            $this->factory->create($attributeValue)->map($product, $attributeValue);
        }
    }
}

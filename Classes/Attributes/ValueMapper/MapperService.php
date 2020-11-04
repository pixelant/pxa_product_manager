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
        $attributesSets = $product->_getAllAttributesSets();

        $this->process($product, $attributesSets);

        return $attributesSets;
    }

    /**
     * Process all attributes.
     *
     * @param Product $product
     * @param array $attributesSets
     */
    protected function process(Product $product, array $attributesSets): void
    {
        $attributes = $this->collection($attributesSets)
            ->pluck('attributes')
            ->shiftLevel()
            ->toArray();

        foreach ($attributes as $attribute) {
            $this->factory->create($attribute)->map($product, $attribute);
        }
    }
}

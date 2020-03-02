<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * Init values for product attribute
 *
 * @package Pixelant\PxaProductManager\Domain\Service
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
            $this->factory->create($attribute)->map($product, $attribute);
        }

        return $attributes;
    }
}

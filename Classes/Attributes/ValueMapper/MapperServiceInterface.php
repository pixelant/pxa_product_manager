<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * Service that should map all product attributes with values.
 */
interface MapperServiceInterface
{
    /**
     * Map all product attributes with values and return result.
     *
     * @param Product $product
     * @return array
     */
    public function map(Product $product): array;
}

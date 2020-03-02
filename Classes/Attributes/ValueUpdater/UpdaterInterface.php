<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueUpdater;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * Interface for product attribute value update
 *
 * @package Pixelant\PxaProductManager\Attributes\ValueUpdater
 */
interface UpdaterInterface
{
    /**
     * Update attribute value
     *
     * @param int|Product $product Product object or uid
     * @param int|Attribute $attribute Attribute object or uid
     * @param mixed $value New value
     * @return void
     */
    public function update($product, $attribute, $value): void;
}

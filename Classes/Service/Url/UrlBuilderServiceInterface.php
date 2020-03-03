<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service\Link;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * @package Pixelant\PxaProductManager\Service\Link
 */
interface UrlBuilderServiceInterface
{
    /**
     * Build url for given category and product.
     * Skip product parameter if only category URL is required
     *
     * @param Category $category
     * @param Product|null $product
     * @return string
     */
    public function url(Category $category, Product $product = null): string;

    /**
     * If URL should be absolute
     *
     * @param bool $absolute
     * @return UrlBuilderServiceInterface
     */
    public function absoluteUrl(Category $category, Product $product = null): string;
}

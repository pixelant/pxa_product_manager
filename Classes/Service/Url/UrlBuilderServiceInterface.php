<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service\Url;

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
     * @param int $pageUid
     * @param Category|null $category
     * @param Product|null $product
     * @return string
     */
    public function url(int $pageUid, ?Category $category, Product $product = null): string;

    /**
     * Build URL only with product parameter, exclude categories
     *
     * @param int $pageUid
     * @param Product $product
     * @return string
     */
    public function productUrl(int $pageUid, Product $product): string;

    /**
     * Flag if builder should use absolute url
     *
     * @param bool $absolute
     * @return void
     */
    public function absolute(bool $absolute): void;
}

<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service\Url;

use Pixelant\PxaProductManager\Domain\Model\Product;

interface UrlBuilderServiceInterface
{
    /**
     * Build url for product.
     *
     * @param Product $product
     * @return string
     */
    public function url(Product $product): string;

    /**
     * Flag if builder should use absolute url.
     *
     * @param bool $absolute
     * @return void
     */
    public function absolute(bool $absolute): void;
}

<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\Product;

use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * @package Pixelant\PxaProductManager\Event\Product
 */
class FormatPrice
{
    /**
     * @var string
     */
    protected string $formattedPrice;

    /**
     * @var Product
     */
    protected Product $product;

    /**
     * @param string $formattedPrice
     * @param Product $product
     */
    public function __construct(string $formattedPrice, Product $product)
    {
        $this->formattedPrice = $formattedPrice;
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function getFormattedPrice(): string
    {
        return $this->formattedPrice;
    }

    /**
     * @param string $formattedPrice
     * @return FormatPrice
     */
    public function setFormattedPrice(string $formattedPrice): FormatPrice
    {
        $this->formattedPrice = $formattedPrice;
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }
}

<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\Product;

use NumberFormatter;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * @package Pixelant\PxaProductManager\Event\Product
 */
class FormatPrice
{
    /**
     * @var NumberFormatter
     */
    protected NumberFormatter $formatter;

    /**
     * @var string
     */
    protected string $currency;

    /**
     * @var string
     */
    protected string $locale;

    /**
     * @var Product
     */
    protected Product $product;

    /**
     * @param NumberFormatter $formatter
     * @param string $currency
     * @param string $locale
     * @param Product $product
     */
    public function __construct(NumberFormatter $formatter, string $currency, string $locale, Product $product)
    {
        $this->formatter = $formatter;
        $this->currency = $currency;
        $this->locale = $locale;
        $this->product = $product;
    }

    /**
     * @return NumberFormatter
     */
    public function getFormatter(): NumberFormatter
    {
        return $this->formatter;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return FormatPrice
     */
    public function setCurrency(string $currency): FormatPrice
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }
}

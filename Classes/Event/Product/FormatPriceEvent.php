<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\Product;

use NumberFormatter;
use Pixelant\PxaProductManager\Domain\Model\Product;

class FormatPriceEvent
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
     * @var int
     */
    protected int $fractionDigits;


    /**
     * @param string $currency
     * @param string $locale
     * @param int $fractionDigits
     */
    public function __construct(string $currency, string $locale, int $fractionDigits)
    {
        $this->currency = $currency;
        $this->locale = $locale;
        $this->fractionDigits = $fractionDigits;
    }

    /**
     * @return NumberFormatter
     */
    public function getFormatter(): NumberFormatter
    {
        $formatter = new NumberFormatter($this->getLocale(), NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $this->getFractionDigits());
        return $formatter;
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
    public function setCurrency(string $currency): self
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
     * @return int
     */
    public function getFractionDigits(): int
    {
        return $this->fractionDigits;
    }
}

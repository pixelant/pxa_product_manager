<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;


use Pixelant\PxaProductManager\Domain\Model\Coupon;
use Pixelant\PxaProductManager\Domain\Model\Order;
use Pixelant\PxaProductManager\Domain\Model\Product;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Handles prices and price calculation.
 *
 * Class PriceService
 * @package Pixelant\PxaProductManager\Service
 */
class PriceService
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Coupon
     */
    protected $coupon;

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return Coupon
     */
    public function getCoupon(): Coupon
    {
        return $this->coupon;
    }

    /**
     * @param Coupon $coupon
     */
    public function setCoupon(array $coupon): self
    {
        $this->coupon = $coupon;
        return $this;
    }

    /**
     * Reset the service properties
     *
     * @return $this
     */
    public function reset()
    {
        $this->order = null;
        $this->product = null;
        $this->coupon = null;

        return $this;
    }

    /**
     * Returns the total net price with all modifiers included
     *
     * @return float
     */
    public function calculateTotalPrice(): float
    {
        // TODO
        return rand(20, 100);
    }

    /**
     * Returns the total tax
     *
     * @return float
     */
    public function caluclateTotalTax(): float
    {
        // TODO
        return rand(1, 10);
    }

    /**
     * Returns the change in value resulting from the applied coupons
     *
     * @return float
     */
    public function calculateTotalCouponValue(): float
    {

    }

    /**
     * Returns the price before tax, but including coupons
     *
     * @return float
     */
    public function calculatePriceBeforeTax(): float
    {

    }

    /**
     * Returns the price before tax and coupons
     *
     * @return float
     */
    public function calculatePriceBeforeTaxAndCoupons(): float
    {

    }

    /**
     * Returns the price including tax, but excluding coupons
     *
     * @return float
     */
    public function calculatePriceBeforeCoupons(): float
    {

    }

    /**
     * Format value an integer using the smallest unit of currency
     *
     * This adheres to the ISO 4217 standard used by most payment gateways
     * https://en.wikipedia.org/wiki/ISO_4217
     *
     * @param float $value Any currency value
     * @param int $fractionalDigits Fractional digits in currency (default is 2)
     * @param string $locale The PHP locale to use. Will override $fractionalDigits
     *
     * @return int
     */
    public static function formatForIso4217(float $value, int $fractionalDigits = 2, string $locale = null)
    {
        if ($locale !== null) {
            $oldLocale = setlocale(LC_ALL, 0);
            setlocale(LC_ALL, $locale);
            $fractionalDigits = localeconv()['int_frac_digits'];
        }

        $convertedValue = (int) pow($value, $fractionalDigits);

        if ($locale !== null) {
            setlocale(LC_ALL, $oldLocale);
        }

        return $convertedValue;
    }
}

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
    public function getOrder(): ?Order
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
    public function getProduct(): ?Product
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
    public function getCoupon(): ?Coupon
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
    public function calculatePrice(): float
    {
        return $this->calculateProductPriceBeforeTaxAndCoupon()
            + $this->calculateTax()
            + $this->calculateCouponValue();
    }

    /**
     * Returns the total tax
     *
     * @return float
     */
    public function calculateTax(): float
    {
        if ($this->order === null && $this->product === null) {
            return 0.0;
        } elseif ($this->order === null) {
            return $this->product->getTax();
        }

        $value = 0.0;

        /** @var Product $product */
        foreach ($this->order->getProducts() as $product) {
            $value += $product->getTaxForCheckout() * $this->order->getProductQuantity($product);
        }

        return $value;
    }

    /**
     * Returns the change in value resulting from the applied coupons
     *
     * @return float
     */
    public function calculateCouponValue(): float
    {
        if (($this->order === null && $this->coupon === null) || ($this->order === null && $this->product === null)) {
            return 0.0;
        }

        $beforePrice = $this->calculateProductPriceBeforeTaxAndCoupon();

        if ($this->order === null) {
            return $this->applyCouponToValue($beforePrice) - $beforePrice;
        }

        return $this->applyOrderCouponsToValue($beforePrice) - $beforePrice;
    }

    /**
     * Returns the price before tax, but including coupons
     *
     * @return float
     */
    public function calculatePriceBeforeTax(): float
    {
        return $this->calculatePriceBeforeTaxAndCoupon() - $this->calculateTax();
    }

    /**
     * Returns the price before tax and coupons
     *
     * @return float
     */
    public function calculatePriceBeforeTaxAndCoupon(): float
    {
        if ($this->order === null || $this->product !== null) {
            return $this->calculateProductPriceBeforeTaxAndCoupon();
        }

        $total = 0.0;

        foreach ($this->order->getProducts() as $product) {
            $this->setProduct($product);
            $total += $this->calculateOrderTotalPriceForProductBeforeTaxAndCoupon();
        }

        $this->product = null;

        return $total;
    }

    /**
     * Returns the price including tax, but excluding coupons
     *
     * @return float
     */
    public function calculatePriceBeforeCoupon(): float
    {
        return $this->calculatePrice() - $this->calculateCouponValue();
    }

    /**
     * Get the product price
     *
     * @return float
     */
    public function calculateProductPriceBeforeTaxAndCoupon(): float
    {
        if ($this->getProduct() === null) {
            return 0.0;
        }

        if($this->order === null) {
            return $this->product->getPrice();
        }

        return $this->product->getPriceForCheckout();
    }

    /**
     * Returns the product total (i.e. price * quantity) for product
     *
     * @return float
     */
    public function calculateOrderTotalForProductBeforeTaxAndCoupon(): float
    {
        if ($this->order === null) {
            return $this->calculateProductPriceBeforeTaxAndCoupon();
        }

        return $this->order->getProductQuantity($this->getProduct()) * $this->calculateProductPriceBeforeTaxAndCoupon();
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

    /**
     * Apply the $this->coupon to the supplied value
     *
     * Returns zero if the resulting sum is less than zero
     *
     * @param float $value
     * @return float
     */
    protected function applyCouponToValue(float $value): float
    {
        if ($this->coupon === null) {
            return $value;
        }

        switch ($this->coupon->getType()) {
            case Coupon::TYPE_CASH_REBATE:
                $value -= $this->coupon->getValue();
                break;
            case Coupon::TYPE_PERCENTAGE_REBATE:
                $value -= $value * ($this->coupon->getValue() / 100);
                break;
        }

        //Make sure the coupon doesn't return a negative value
        if ($value < 0) {
            $value = 0.0;
        }

        return $value;
    }

    /**
     * Applies the coupons in $this->order to the supplied value
     *
     * @param float $value
     * @return float
     */
    protected function applyOrderCouponsToValue(float $value): float
    {
        if ($this->order === null) {
            return $value;
        }

        $previousCoupon = $this->coupon;

        foreach ($this->order->getCoupons() as $coupon) {
            $this->setCoupon($coupon);
            $value = $this->applyCouponToValue($value);
        }

        $this->coupon = $previousCoupon;

        return $value;
    }
}

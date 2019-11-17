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
    public function setCoupon(Coupon $coupon): self
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
     * Resets the order
     *
     * @return $this;
     */
    public function resetOrder(): self
    {
        $this->order = null;

        return $this;
    }

    /**
     * Resets the product
     *
     * @return $this;
     */
    public function resetProduct(): self
    {
        $this->product = null;

        return $this;
    }

    /**
     * Resets the coupon
     *
     * @return $this;
     */
    public function resetCoupon(): self
    {
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
        return $this->applyOrderCouponsToValue($this->calculatePriceBeforeTaxAndCoupon() + $this->calculateTax());
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
     * Returns the change in value before tax resulting from the applied coupons
     *
     * @return float
     */
    public function calculateCouponValueBeforeTax(): float
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
     * Returns the change in value resulting from the applied coupons
     *
     * @return float
     */
    public function calculateCouponValue(): float
    {
        if (($this->order === null && $this->coupon === null) || ($this->order === null && $this->product === null)) {
            return 0.0;
        }

        $beforePrice = $this->calculateProductPrice();

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
        return $this->calculatePriceBeforeTaxAndCoupon() + $this->calculateCouponValue();
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
            $this->product = $product;
            $total += $this->calculateOrderTotalForProductBeforeTaxAndCoupon();
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
     * Calculates the tax for $this->product, including coupons
     *
     * @return float
     */
    public function calculateProductTax()
    {
        if ($this->getProduct() === null) {
            return 0.0;
        }

        if($this->order === null) {
            return $this->applyCouponToValue($this->product->getTax());
        }

        return $this->applyOrderCouponsToValue($this->product->getTaxForCheckout());
    }

    /**
     * The product price without coupon, but including tax
     *
     * @return float
     */
    public function calculateProductPriceBeforeCoupon(): float
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
     * The product price including tax and coupon
     *
     * @return float
     */
    public function calculateProductPrice(): float
    {
        if ($this->product === null) {
            return 0.0;
        }

        if($this->order === null) {
            return $this->applyCouponToValue($this->calculateProductPriceBeforeCoupon());
        }

        return $this->applyOrderCouponsToValue($this->calculateProductPriceBeforeCoupon());
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
            return $this->product->getPrice() - $this->product->getTax();
        }

        return $this->product->getPriceForCheckout() - $this->product->getTaxForCheckout();
    }

    /**
     * Returns the product total (i.e. price * quantity) for product without tax and coupon codes
     *
     * @return float
     */
    public function calculateOrderTotalForProductBeforeTaxAndCoupon(): float
    {
        if ($this->product === null) {
            return 0.0;
        }

        if ($this->order === null) {
            return $this->calculateProductPriceBeforeTaxAndCoupon();
        }

        return $this->order->getProductQuantity($this->getProduct()) * $this->calculateProductPriceBeforeTaxAndCoupon();
    }

    /**
     * Returns the product product in the order (i.e. price * quantity)
     *
     * @return float
     */
    public function calculateOrderTotalForProduct(): float
    {
        if ($this->product === null) {
            return 0.0;
        }

        if ($this->order === null) {
            return $this->calculatePrice();
        }

        return $this->order->getProductQuantity($this->getProduct()) * $this->calculateProductPrice();
    }

    /**
     * Returns the coupon value for the product in the order
     *
     * The resulting sum is the amount discounted from this product (including quantity) in the order.
     * For discounts, it will be a positive sum.
     *
     * @return float
     */
    public function calculateOrderTotalCouponValueForProduct(): float
    {
        if ($this->product === null) {
            return 0.0;
        }

        if ($this->order === null) {
            return $this->calculateCouponValue();
        }

        return $this->applyOrderCouponsToValue($this->calculateOrderTotalForProduct()) - $this->calculateOrderTotalForProduct();
    }

    /**
     * Returns the total tax for within an order (i.e. tax * quantity) $this->product
     *
     * @return float
     */
    public function calculateOrderTotalTaxForProduct(): float
    {
        if ($this->product === null) {
            return 0.0;
        }

        if ($this->order === null) {
            return $this->calculateProductTax();
        }

        return $this->applyOrderCouponsToValue($this->order->getProductQuantity($this->getProduct()) * $this->calculateProductTax());
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

        $convertedValue = (int) ($value * pow(10, $fractionalDigits));

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

    /**
     * Internal debugging function
     *
     * @internal
     * @return array
     */
    public function debugPrices():array
    {
        return [
            'order' => $this->order !== null ? $this->order->getUid() : '',
            'product' => $this->product !== null ? $this->product->getUid() : '',
            'coupon' => $this->coupon !== null ? $this->coupon->getUid() : '',
            'productQuantitiesInOrder' => $this->order !== null ? $this->order->getProductsQuantity() : '',
            'calculateCouponValue' => $this->calculateCouponValue(),
            'calculateCouponValueBeforeTax' => $this->calculateCouponValueBeforeTax(),
            'calculateOrderTotalCouponValueForProduct' => $this->calculateOrderTotalCouponValueForProduct(),
            'calculateOrderTotalForProduct' => $this->calculateOrderTotalForProduct(),
            'calculateOrderTotalForProductBeforeTaxAndCoupon' => $this->calculateOrderTotalForProductBeforeTaxAndCoupon(),
            'calculateOrderTotalTaxForProduct' => $this->calculateOrderTotalTaxForProduct(),
            'calculatePrice' => $this->calculatePrice(),
            'formatForIso4217(calculatePrice())' => self::formatForIso4217($this->calculatePrice()),
            'calculatePriceBeforeCoupon' => $this->calculatePriceBeforeCoupon(),
            'calculatePriceBeforeTax' => $this->calculatePriceBeforeTax(),
            'calculatePriceBeforeTaxAndCoupon' => $this->calculatePriceBeforeTaxAndCoupon(),
            'calculateProductPrice' => $this->calculateProductPrice(),
            'calculateProductPriceBeforeCoupon' => $this->calculateProductPriceBeforeCoupon(),
            'calculateProductPriceBeforeTaxAndCoupon' => $this->calculateProductPriceBeforeTaxAndCoupon(),
            'calculateProductTax' => $this->calculateProductTax(),
            'calculateTax' => $this->calculateTax()
        ];
    }
}

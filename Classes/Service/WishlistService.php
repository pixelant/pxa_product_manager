<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;

use Pixelant\PxaProductManager\Utility\OrderUtility;

/**
 * Class WishlistService
 * @package Pixelant\PxaProductManager\Service
 */
class WishlistService
{
    public function hasProduct()
    {
    }

    public function productsCount()
    {
        $order = OrderUtility::getSessionOrder();
        return $order->getNumberOfProducts();
    }
}

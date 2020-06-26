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
    /**
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function productsCount()
    {
        if (! OrderUtility::sessionOrderExists()) {
            return 0;
        }

        $order = OrderUtility::getSessionOrder();
        return $order->getNumberOfProducts();
    }
}

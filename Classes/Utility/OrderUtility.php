<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

use Pixelant\PxaProductManager\Domain\Model\Coupon;
use Pixelant\PxaProductManager\Domain\Model\Order;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\OrderRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Exception\UnknownProductException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Pavlo Zaporozkyi <pavlo@pixelant.se>, Pixelant
 *
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
 * Class for handling order-related actions
 *
 * Currently related to session order persistence
 *
 * Class OrderUtility
 * @package Pixelant\PxaProductManager\Utility
 */
class OrderUtility
{
    const SESSION_KEY = 'tx_pxa_product_manager_sessionOrder';

    const MAX_PRODUCTS = 20;

    /**
     * @var Order $sessionOrder
     */
    protected static $sessionOrder = null;

    /**
     * Fetches the current session's order
     *
     * @return Order
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public static function getSessionOrder()
    {
        /** @var OrderRepository $orderRepository */
        $orderRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(OrderRepository::class);

        $orderUid = (int) $GLOBALS['TSFE']->fe_user->getKey('ses', self::SESSION_KEY);

        if ($orderUid > 0) {
            /** @var Order $order */
            if (self::$sessionOrder !== null) {
                $order = self::$sessionOrder;
            } else {
                $order = $orderRepository->findByUid($orderUid);
                self::$sessionOrder = $order;
            }

            if ($order !== null && !$order->isComplete()) {
                return $order;
            } elseif ($order->isComplete()) {
                self::$sessionOrder = null;
            }
        }

        $order = MainUtility::getObjectManager()->get(Order::class);

        $orderRepository->add($order);

        /** @var PersistenceManagerInterface $persistanceManager */
        $persistanceManager = GeneralUtility::makeInstance(PersistenceManager::class);

        //Make sure we get a UID
        $persistanceManager->persistAll();

        $GLOBALS['TSFE']->fe_user->setKey('ses', self::SESSION_KEY, $order->getUid());

        return $order;
    }

    /**
     * @return bool
     */
    public static function sessionOrderExists()
    {
        $orderUid = (int) $GLOBALS['TSFE']->fe_user->getKey('ses', self::SESSION_KEY);
        return $orderUid > 0;
    }

    /**
     * Adds a product to the session order using the product's UID
     *
     * @param int $uid
     * @throws UnknownProductException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public static function addProductUidToSessionOrder(int $uid)
    {
        self::addProductToSessionOrder(ProductUtility::getProductByUid($uid));
    }

    /**
     * Removed a product UID from the order
     *
     * @param int $uid
     * @throws UnknownProductException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public static function removeProductUidFromSessionOrder(int $uid)
    {
         self::removeProductFromSessionOrder(ProductUtility::getProductByUid($uid));
    }

    /**
     * Adds a product to the session order
     *
     * @param Product $product
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public static function addProductToSessionOrder(Product $product)
    {
        $wishListLimitFromSettings = ConfigurationUtility::getSettingsByPath('wishList/limit');

        $order = self::getSessionOrder();

        //If there are too many products, remove one.
        if (
            ((int) $wishListLimitFromSettings > 0 && $order->getProductsQuantityTotal() + 1 > (int) $wishListLimitFromSettings)
            ||
            ((int) $wishListLimitFromSettings === 0 && $order->getProductsQuantityTotal() + 1 > self::MAX_PRODUCTS)
        ) {
            $order->getProducts()->rewind();
            if ($order->getProductQuantity($order->getProducts()->current()) <= 1) {
                $order->removeProduct($order->getProducts()->current());
            } else {
                $order->setProductQuantity($order->getProducts()->current(), $order->getProductQuantity($order->getProducts()->current()) - 1);
            }
        }

        $order->addProduct($product);

        self::updateOrder($order);
    }

    /**
     * Remove a product from the session order
     *
     * @param Product $product
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public static function removeProductFromSessionOrder(Product $product)
    {
        $order = self::getSessionOrder();

        $order->removeProduct($product);

        self::updateOrder($order);
    }

    /**
     * Unsets the Order uid stored in session
     *
     * Call if the order has been completed or otherwise shouldn't be related to the session any longer.
     */
    public static function removeOrderFromSession()
    {
        $GLOBALS['TSFE']->fe_user->setKey('ses', self::SESSION_KEY, null);
    }

    /**
     * Convenience function for running OrderRepository->update()
     *
     * @param Order $order
     */
    public static function updateOrder(Order $order)
    {
        GeneralUtility::makeInstance(ObjectManager::class)->get(OrderRepository::class)->update($order);
    }

    /**
     * Returns a hash based on products, product count, and coupons
     *
     * Hookable with $GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['Utility/OrderUtility.php']['calculateOrderStateHash']
     * which receives $hashBaseArray as parameter and $order as reference.
     *
     * @param Order $order
     *
     * @return string
     */
    public static function calculateOrderStateHash(Order $order): string
    {
        $hashBaseArray = [];

        //Add products and product quantities
        $hashBaseArray['products'] = [];
        /** @var Product $product */
        foreach ($order->getProducts() as $product) {
            $hashBaseArray['products'][] = [
                'uid' => $product->getUid(),
                'quantity' => $order->getProductQuantity($product)
            ];
        }

        //Add coupons
        $hashBaseArray['coupons'] = [];
        /** @var Coupon $coupon */
        foreach ($order->getCoupons() as $coupon) {
            $hashBaseArray['coupons'] = [
                'uid' => $coupon->getUid()
            ];
        }

        //Handle $hashBaseArray additions through a hook
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['Utility/OrderUtility.php']['calculateOrderStateHash'])) {
            foreach($GLOBALS['TYPO3_CONF_VARS']['EXT']['pxa_product_manager']['Utility/OrderUtility.php']['calculateOrderStateHash'] as $functionReference) {
                $hashBaseArray = GeneralUtility::callUserFunction($functionReference, $hashBaseArray, $order);
            }
        }

        return md5(serialize($hashBaseArray));
    }
}

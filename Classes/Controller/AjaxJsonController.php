<?php


namespace Pixelant\PxaProductManager\Controller;

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

use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Exception\InvalidPriceCalculationException;
use Pixelant\PxaProductManager\Factory\PriceServiceFactory;
use Pixelant\PxaProductManager\Service\WishlistService;
use Pixelant\PxaProductManager\Utility\MainUtility;
use Pixelant\PxaProductManager\Utility\OrderUtility;
use Pixelant\PxaProductManager\Utility\ProductUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;

/**
 * Class AjaxJsonController
 * @package Pixelant\PxaProductManager\Controller
 */
class AjaxJsonController extends AbstractController
{
    /**
     * Default view
     *
     * @var string
     */
    protected $defaultViewObjectName = JsonView::class;

    /**
     * Add/remove product in wish list
     *
     * @param Product $wishProduct
     */
    public function toggleWishListAction(Product $wishProduct = null)
    {
        $order = OrderUtility::getSessionOrder();
        $response = [
            'success' => false,
        ];

        $limit = (int)$this->settings['wishList']['limit'];

        if ($wishProduct !== null) {
            $inWishList = ProductUtility::isProductInWishList($wishProduct);
            $response['inList'] = !$inWishList;

            if ($inWishList) {
                $order->removeProduct($wishProduct);

                $this->orderRepository->update($order);

                $response['success'] = true;
                $message = $this->translate(
                    'fe.remove_from_list',
                    [
                        $this->translate('fe.wish_list')
                    ]
                );
            } else {
                if ($order->getProductsQuantityTotal() + 1 > $limit) {
                    $message = $this->translate('fe.error_limit');
                    unset($response['inList']);
                } else {
                    $order->addProduct($wishProduct);

                    $this->orderRepository->update($order);

                    $response['success'] = true;

                    $message = $this->translate(
                        'fe.added_to_list',
                        [
                            $this->translate('fe.wish_list')
                        ]
                    );
                }
            }
        }

        $response['message'] = $message ?? $this->translate('fe.error_request');

        $this->view->assign('value', $response);
    }

    /**
     * Add/Remove from compare list
     *
     * @param Product|null $compareProduct
     */
    public function toggleCompareListAction(Product $compareProduct = null)
    {
        $response = [
            'success' => false,
            'inList' => false
        ];

        if ($compareProduct !== null) {
            $compareList = MainUtility::getTSFE()->fe_user->getKey(
                'ses',
                ProductUtility::COMPARE_LIST_SESSION_NAME
            );

            if (!is_array($compareList)) {
                $compareList = [];
            }

            if (($key = array_search($compareProduct->getUid(), $compareList, true)) !== false) {
                unset($compareList[$key]);
                $translationKey = 'fe.remove_from_list';
            } else {
                $compareList[] = $compareProduct->getUid();
                $response['inList'] = true;
                $translationKey = 'fe.added_to_list';
            }

            MainUtility::getTSFE()->fe_user->setKey('ses', ProductUtility::COMPARE_LIST_SESSION_NAME, $compareList);
            $message = $this->translate(
                $translationKey,
                [
                    $this->translate('fe.compare_list')
                ]
            );
            $response['success'] = true;
        }

        $response['message'] = $message ?? $this->translate('fe.error_request');
        $response['updatedList'] = $compareList ?? [];

        $this->view->assign('value', $response);
    }

    /**
     * Compare list
     */
    public function loadCompareListAction()
    {
        $compareList = MainUtility::getTSFE()->fe_user->getKey('ses', ProductUtility::COMPARE_LIST_SESSION_NAME);

        $this->view->assign('value', ['compareList' => $compareList ?? []]);
    }

    /**
     * Compare list
     */
    public function emptyCompareListAction()
    {
        MainUtility::getTSFE()->fe_user->setKey('ses', ProductUtility::COMPARE_LIST_SESSION_NAME, []);

        $this->view->assign('value', ['success' => true]);
    }

    /**
     * Load whishlist action
     */
    public function loadWishListAction()
    {
        $wishList = ProductUtility::getWishList();

        $this->view->assign('value', ['wishList' => $wishList ?? []]);
    }

    /**
     * Add product to latest visited
     *
     * @param Product $product
     */
    public function addLatestVisitedProductAction(Product $product)
    {
        MainUtility::addValueToListCookie(
            ProductUtility::LATEST_VISITED_COOKIE_NAME,
            $product->getUid(),
            ((int)$this->settings['latestVisitedProductsLimit'] + 1)
        );

        $this->view->assign('value', ['success' => true]);
    }

    /**
     * @return mixed
     */
    public function totalOrderPricesAction()
    {
        try {
            $priceService = (new PriceServiceFactory())->createFromSession();

            $totalPrice = $priceService->calculatePrice();
            $totalTaxPrice = $priceService->calculateTax();

            $orderWithoutCoupon = clone $priceService->getOrder();
            foreach ($orderWithoutCoupon->getCoupons() as $coupon) {
                $orderWithoutCoupon->removeCoupon($coupon);
            }

            $totalPriceThereafter = $priceService->calculatePrice();
            $totalTaxPriceThereafter = $priceService->calculateTax();
        } catch (\Exception $e) {
            $this->response->setStatus(500, 'Price calculation error');
            return null;
        }

        if ($totalPrice < 0 || $totalTaxPrice < 0) {
            $this->response->setStatus(500, 'Price calculation error');
            return null;
        }

        $response = [
            'totalPrice' => $totalPrice,
            'totalTaxPrice' => $totalTaxPrice,
            'totalPriceThereafter' => $totalPriceThereafter,
            'totalTaxPriceThereafter' => $totalTaxPriceThereafter
        ];

        $this->response->setStatus(200, 'OK');
        $this->view->assign('value', $response);
    }

    /**
     * @return int
     */
    public function wishlistProductsCountAction()
    {
        /** @var WishlistService $wishlistService */
        $wishlistService = GeneralUtility::makeInstance(WishlistService::class);
        $this->response->setStatus(200, 'OK');
        $this->view->assign('value', $wishlistService->productsCount() ?? 0);
    }

    /**
     * @return bool
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function updateOrderQuantitiesAction()
    {
        if (!GeneralUtility::_GP('quantities')) {
            $this->response->setStatus(400, 'Quantities are required');
            return false;
        }

        $quantities = GeneralUtility::_GP('quantities');

        // REST violation yay!!! :D
        $order = OrderUtility::getSessionOrder();

        if (!$order) {
            $this->response->setStatus(400, 'Order not found');
            return false;
        }

        // Dont want to just set the whole quantities field in case it will contain incorrect data
        /** @var Product $product */
        foreach ($order->getProducts() as $product) {
            $uid = $product->getUid();
            if (array_key_exists($uid, $quantities)) {
                $order->setProductQuantity($product, $quantities[$uid]);
            }
        }

        $this->orderRepository->update($order);

        $response = null;
        $this->response->setStatus(200, 'OK');
        $this->view->assign('value', $response);
        return true;
    }
}

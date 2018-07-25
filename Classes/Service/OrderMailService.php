<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;

use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Utility\MainUtility;
use Pixelant\PxaProductManager\Utility\ProductUtility;

/**
 * Class OrderMailService
 * @package Pixelant\PxaProductManager\Service
 */
class OrderMailService extends AbstractMailService
{
    /**
     * Prepare body to send
     * @param mixed ...$variables
     * @return OrderMailService
     * @throws \Pixelant\PxaProductManager\Exception\OrderEmailException
     */
    public function generateMailBody(...$variables)
    {
        list($template, $order) = $variables;

        $standAloneView = $this->initializeStandaloneView(
            $template
        );

        if (MainUtility::isPricingEnabled()) {
            $totalPrice = 0.00;
            $orderProductsQuantity = $order->getProductsQuantity();

            /** @var Product $product */
            foreach ($order->getProducts() as $product) {
                $totalPrice += ($product->getPrice() * (int)($orderProductsQuantity[$product->getUid()] ?? 1));
            }
            
            $standAloneView->assign('totalPrice', ProductUtility::formatPrice($totalPrice));
        }

        $standAloneView->assign('order', $order);

        $this->message = $standAloneView->render();

        return $this;
    }
}

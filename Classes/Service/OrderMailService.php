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
        list($template, $orderFields, $orderProducts, $products) = $variables;

        $standAloneView = $this->initializeStandaloneView(
            $template
        );

        if (MainUtility::isPricingEnabled()) {
            $totalPrice = 0.00;
            /** @var Product $product */
            foreach ($products as $product) {
                $totalPrice += ($product->getPrice() * (int)($orderProducts[$product->getUid()] ?? 1));
            }
        }

        $standAloneView->assignMultiple([
            'orderFields' => $orderFields,
            'orderProducts' => $orderProducts,
            'products' => $products,
            'totalPrice' => ProductUtility::formatPrice($totalPrice)
        ]);

        $this->message = $standAloneView->render();

        return $this;
    }
}

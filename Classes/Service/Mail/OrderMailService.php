<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service\Mail;

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
            $standAloneView->assign('totalPrice', ProductUtility::calculateOrderTotalPrice($order, true));
            $standAloneView->assign('totalTax', ProductUtility::calculateOrderTotalTax($order, true));
        }

        $standAloneView->assign('order', $order);

        $this->message = $standAloneView->render();

        return $this;
    }
}

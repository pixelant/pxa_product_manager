<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;

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
        list($template, $orderFields) = $variables;

        $standAloneView = $this->initializeStandaloneView(
            $template
        );

        $this->message = $standAloneView->render();

        return $this;
    }
}

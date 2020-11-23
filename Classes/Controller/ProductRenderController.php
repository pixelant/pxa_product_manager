<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

class ProductRenderController extends AbstractController
{
    /**
     * @param int|null $productId
     */
    public function initAction(int $productId = null): void
    {
        if ($productId === null) {
            $this->view->assign('view', 'list');
        } else {
            $this->view->assignMultiple(['view' => 'single', 'productId' => $productId]);
        }
    }
}

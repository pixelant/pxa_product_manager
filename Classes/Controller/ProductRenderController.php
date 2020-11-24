<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Model\Product;

class ProductRenderController extends AbstractController
{
    /**
     * @param Product|null $product
     */
    public function initAction(Product $product = null): void
    {
        if ($product === null) {
            $this->view->assign('view', 'list');
        } else {
            $this->view->assignMultiple(['view' => 'single', 'product' => $product]);
        }
    }
}

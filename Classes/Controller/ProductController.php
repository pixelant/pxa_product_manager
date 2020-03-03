<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * @package Pixelant\PxaProductManager\Controller
 */
class ProductController extends ActionController
{
    protected ProductRepository $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function injectProductRepository(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * List action
     */
    public function listAction()
    {
        $this->view->assign('products', $this->productRepository->findAll());
    }
}

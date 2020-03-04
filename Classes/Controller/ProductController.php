<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Model\DTO\Factory\ProductDemandFactory;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * @package Pixelant\PxaProductManager\Controller
 */
class ProductController extends ActionController
{
    /**
     * @var ProductRepository
     */
    protected ProductRepository $productRepository;

    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $categoryRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function injectProductRepository(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * List action
     *
     * @param Category|null $category
     */
    public function listAction(Category $category = null)
    {
        if ($category === null) {
            $category = $this->categoryRepository->findByUid(
                (int)$this->settings['list']['entryNavigationCategory']
            );
        }

        $products = $this->productRepository->findDemanded(
            $this->createDemandObject($this->settings + ['categories' => [$category]])
        );

        $this->view->assignMultiple(compact('category', 'products'));
    }

    /**
     * Show product
     *
     * @param Product $product
     * @param Category|null $category
     */
    public function showAction(Product $product, Category $category = null)
    {
        $this->view->assignMultiple(compact('product', 'category'));
    }

    /**
     * Create product demand
     *
     * @param array $settings
     * @return DemandInterface
     */
    protected function createDemandObject(array $settings): DemandInterface
    {
        return $this->objectManager->get(ProductDemandFactory::class)->buildFromSettings(
            $settings,
            $settings['demand']['objects']['productDemand']
        );
    }
}

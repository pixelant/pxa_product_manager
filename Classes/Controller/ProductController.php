<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Model\DTO\Factory\ProductDemandFactory;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
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

        /** @var ProductDemand $demand */
        $demand = $this->createDemandObject($this->settings);
        $demand->setCategories([$category]);

        $products = $this->productRepository->findDemanded($demand);

        $this->view->assignMultiple(compact('category', 'products'));
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

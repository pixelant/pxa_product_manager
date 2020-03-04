<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Model\DTO\Factory\CategoryDemandFactory;
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

        $subCategories = $this->categoryRepository->findDemanded(
            $this->createDemand(CategoryDemandFactory::class, $this->settings)
                ->setParent($category)
                ->setOnlyVisibleInNavigation(true)
        );
        $products = $this->productRepository->findDemanded(
            $this->createDemand(ProductDemandFactory::class, $this->settings)->setCategories([$category])
        );

        $this->view->assignMultiple(compact('category', 'subCategories', 'products'));
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
     * Create category demand
     *
     * @param string $factory
     * @param array $settings
     * @return DemandInterface
     */
    protected function createDemand(string $factory, array $settings): DemandInterface
    {
        return $this->objectManager->get($factory)->buildFromSettings($settings);
    }
}

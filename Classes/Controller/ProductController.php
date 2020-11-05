<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;

class ProductController extends AbstractController
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
    public function injectProductRepository(ProductRepository $productRepository): void
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository): void
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * List action.
     *
     * @param Category|null $category
     */
    public function listAction(Category $category = null): void
    {
        $category ??= $this->categoryRepository->findByUid((int)$this->settings['list']['entryNavigationCategory']);

        $categoryDemand = $this->createCategoriesDemand(
            $this->settings + ['parent' => $category, 'onlyVisibleInNavigation' => true]
        );
        $productDemand = $this->createProductsDemand(['categories' => [$category]] + $this->settings);

        $this->view->assignMultiple([
            'category' => $category,
            'subCategories' => $this->categoryRepository->findDemanded($categoryDemand),
            'products' => $this->productRepository->findDemanded($productDemand),
        ]);
    }

    /**
     * Show product.
     *
     * @param Product $product
     * @param Category|null $category
     */
    public function showAction(Product $product, Category $category = null): void
    {
        $this->view->assignMultiple(compact('product', 'category'));
    }
}

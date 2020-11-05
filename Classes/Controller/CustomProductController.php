<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class CustomProductController extends AbstractController
{
    use CanCreateCollection;

    /**
     * @var ProductRepository
     */
    protected ProductRepository $productRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

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
     * Selection of products.
     */
    public function listAction(): void
    {
        switch ($this->settings['customProductsList']['mode']) {
            case 'products':
                $products = $this->findRecordsByList(
                    $this->settings['customProductsList']['products'],
                    $this->productRepository
                );

                break;
            case 'category':
                $categories = $this->customCategoriesWithProducts();

                break;
            default:
                $categories = [];
                $products = $categories;
        }

        $this->view->assignMultiple(compact('products', 'categories'));
    }

    /**
     * Find selected categories for custom view and set products.
     *
     * @return Category[]
     */
    protected function customCategoriesWithProducts(): array
    {
        $categories = $this->findRecordsByList(
            $this->settings['customProductsList']['categories'],
            $this->categoryRepository
        );

        /** @var ProductDemand $demand */
        $demand = $this->createProductsDemand($this->settings);

        /** @var Category $category */
        foreach ($categories as $category) {
            $demand->setCategories([$category]);

            $productsStorage = new ObjectStorage();
            foreach ($this->productRepository->findDemanded($demand) as $product) {
                $productsStorage->attach($product);
            }

            // Set category products, but from demand result
            $category->setProducts($productsStorage);
        }

        return $categories;
    }
}

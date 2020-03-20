<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility as GU;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @package Pixelant\PxaProductManager\Controller
 */
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
     * Selection of products
     */
    public function listAction()
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
                $products = $categories = [];
        }

        $this->view->assignMultiple(compact('products', 'categories'));
    }

    /**
     * Find selected categories for custom view and set products
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

    /**
     * Find records by uids and sort in same order
     *
     * @param string $list
     * @param Repository $repository
     * @return array
     */
    protected function findRecordsByList(string $list, Repository $repository): array
    {
        $uids = GU::intExplode(',', $list, true);
        if (! empty($uids)) {
            $products = $repository->findByUids($uids)->toArray();
            return $this->collection($products)->sortByOrderList($uids, 'uid')->toArray();
        }

        return [];
    }
}

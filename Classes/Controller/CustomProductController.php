<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility as GU;
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
                $products = $this->findProductByList($this->settings['customProductsList']['products']);
                break;
            case 'category':
                $categories = $this->findCategoriesByList($this->settings['customProductsList']['categories']);
                break;
            default:
                $products = $categories = [];
        }

        $this->view->assignMultiple(compact('products', 'categories'));
    }

    /**
     * Find list of products
     *
     * @param string $list
     * @return array
     */
    protected function findCategoriesByList(string $list): array
    {
        return $this->findRecordsByList($list, $this->categoryRepository);
    }

    /**
     * Find list of products
     *
     * @param string $list
     * @return array
     */
    protected function findProductByList(string $list): array
    {
        return $this->findRecordsByList($list, $this->productRepository);
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

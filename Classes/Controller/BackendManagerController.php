<?php

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Model\Category;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class BackendManagerController
 * @package Pixelant\PxaProductManager\Controller
 */
class BackendManagerController extends ActionController
{
    /**
     * BackendTemplateContainer
     *
     * @var BackendTemplateView
     */
    protected $view = null;

    /**
     * Backend Template Container
     *
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var \Pixelant\PxaProductManager\Domain\Repository\ProductRepository
     * @inject
     */
    protected $productRepository = null;

    /**
     * @var \Pixelant\PxaProductManager\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository = null;

    /**
     * Current page
     *
     * @var int
     */
    protected $pid = 0;

    /**
     * Initialize basic vars
     */
    public function initializeAction()
    {
        $this->pid = (int)GeneralUtility::_GET('id');
    }

    /**
     * Main view
     *
     * @param Category $category
     */
    public function indexAction(Category $category = null)
    {
        if ($this->pid > 0) {
            $categories = $this->categoryRepository->findCategoriesByPidAndParentIgnoreHidden(
                $this->pid,
                $category
            );

            $this->view->assignMultiple([
                'categories' => $categories,
                'products' => $this->getCategoriesWithProducts($categories),
                'categoriesPositions' => $this->generatePositionsArray($categories->toArray()),
                'activeCategory' => $category,
                'newCategoryUrl' => $this->buildNewRecordLink('sys_category', $category),
                'categoryBreadCrumbs' => $this->buildCategoryBreadCrumbs($category),
                'pageTitle' => BackendUtility::getRecord('pages', $this->pid, 'title')['title']
            ]);
        }
    }

    /**
     * Get categories uid to products array
     *
     * @param QueryResultInterface|array $categories
     * @return array
     */
    protected function getCategoriesWithProducts($categories): array
    {
        $products = [];

        /** @var Category $category */
        foreach ($categories as $category) {
            $products[$category->getUid()] = $this->productRepository->findProductsByCategories([$category], true);
        }

        return $products;
    }

    /**
     * Get categories bread crumbs
     *
     * @param Category|null $category
     * @return array
     */
    protected function buildCategoryBreadCrumbs(Category $category = null): array
    {
        $result = [];

        while ($category !== null) {
            $result[] = $category;
            $category = $category->getParent();
        }

        return array_reverse($result);
    }

    /**
     * New record url
     * @param string $table
     * @param Category|null $category
     * @return string
     */
    protected function buildNewRecordLink(string $table, Category $category = null): string
    {
        $urlParameters = [
            'edit[' . $table . '][' . $this->pid . ']' => 'new',
            'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
        ];

        if ($category !== null) {
            $field = $table === 'sys_category' ? 'parent' : 'categories';
            $urlParameters['overrideVals'][$table][$field] = $category->getUid();
        }

        return BackendUtility::getModuleUrl('record_edit', $urlParameters);
    }

    /**
     * Generate array of positions uids to sort records
     *
     * @param array|QueryResultInterface $records
     * @return array
     */
    protected function generatePositionsArray($records): array
    {
        if (count($records) < 2) {
            return [];
        }

        $result = [];
        $prevUid = 0;
        $prevPrevUid = 0;

        /** @var AbstractDomainObject $record */
        foreach ($records as $record) {
            if ($prevUid) {
                $result['prev'][$record->getUid()] = $prevPrevUid;
                $result['next'][$prevUid] = '-' . $record->getUid();
            }
            $prevPrevUid = isset($result['prev'][$record->getUid()]) ? -$prevUid : $this->pid;
            $prevUid = $record->getUid();
        }

        return $result;
    }
}

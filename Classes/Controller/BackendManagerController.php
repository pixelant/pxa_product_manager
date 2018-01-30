<?php

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

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
            $categories = $this->categoryRepository->findCategoriesByPidAndParent(
                $this->pid,
                $category
            );

            $this->view
                ->assign('categories', $categories)
                ->assign('activeCategory', $category)
                ->assign('categoryBreadCrumbs', $this->buildCategoryBreadCrumbs($category))
                ->assign('pageTitle', BackendUtility::getRecord('pages', $this->pid, 'title')['title']);
        }
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
}

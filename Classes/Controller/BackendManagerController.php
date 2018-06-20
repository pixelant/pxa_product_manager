<?php

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Traits\TranslateBeTrait;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class BackendManagerController
 * @package Pixelant\PxaProductManager\Controller
 */
class BackendManagerController extends ActionController
{
    use TranslateBeTrait;

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
     * @var \Pixelant\PxaProductManager\Domain\Repository\OrderRepository
     * @inject
     */
    protected $orderRepository = null;

    /**
     * Current page
     *
     * @var int
     */
    protected $pid = 0;

    /**
     * Set up the doc header properly here
     *
     * @param ViewInterface $view
     * @return void
     */
    protected function initializeView(ViewInterface $view)
    {
        /** @var BackendTemplateView $view */
        parent::initializeView($view);

        // create select box menu
        $this->createMenu();
    }

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
    public function indexAction()
    {
    }

    /**
     * Order view
     *
     * @param int $activeTab - Current active tab. 1 - All orders, 2 - Deleted, 0 default new
     */
    public function listOrdersAction(int $activeTab = 0)
    {
        if ($this->pid > 0) {
            $allOrders = $this->orderRepository->findAllInRootLine($this->pid);
            $this->view->assignMultiple([
                'listOrders' => $allOrders,
                'activeTab' => $activeTab,
                'pageTitle' => BackendUtility::getRecord('pages', $this->pid, 'title')['title']
            ]);
        }
    }

    /**
     * List of categories
     *
     * @param Category|null $category
     */
    public function listCategoriesAction(Category $category = null)
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
                'newRecordUrl' => $this->buildNewRecordUrl('sys_category', $category),
                'categoryBreadCrumbs' => $this->buildCategoryBreadCrumbs($category),
                'pageTitle' => BackendUtility::getRecord('pages', $this->pid, 'title')['title']
            ]);
        }
    }

    /**
     * List category products
     *
     * @param Category $category
     */
    public function listProductsAction(Category $category)
    {
        $products = $this->productRepository->findProductsByCategories([$category], true);

        $this->view->assignMultiple([
            'products' => $products,
            'activeCategory' => $category,
            'productsPositions' => $this->generatePositionsArray($products),
            'newRecordUrl' => $this->buildNewRecordUrl('tx_pxaproductmanager_domain_model_product', $category),
            'categoryBreadCrumbs' => $this->buildCategoryBreadCrumbs($category),
            'pageTitle' => BackendUtility::getRecord('pages', $this->pid, 'title')['title']
        ]);
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
    protected function buildNewRecordUrl(string $table, Category $category = null): string
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

    /**
     * create BE menu
     *
     * @return void
     */
    protected function createMenu()
    {
        // if view was found
        if ($this->view->getModuleTemplate() !== null) {
            /** @var UriBuilder $uriBuilder */
            $uriBuilder = $this->objectManager->get(UriBuilder::class);
            $uriBuilder->setRequest($this->request);

            $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
            $menu->setIdentifier('pxa_product_manager');

            $actions = [
                'index' => 'be.action.index',
                'listCategories' => 'be.action.listCategories',
                'listOrders' => 'be.action.listOrders'
            ];

            foreach ($actions as $action => $label) {
                $item = $menu->makeMenuItem()
                    ->setTitle($this->translate($label))
                    ->setHref($uriBuilder->reset()->uriFor($action, [], 'BackendManager'))
                    ->setActive($this->request->getControllerActionName() === $action);
                $menu->addMenuItem($item);
            }

            $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
        }
    }
}

<?php

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\OrderRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Traits\TranslateBeTrait;
use Pixelant\PxaProductManager\Utility\ProductUtility;
use TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

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
     * @var ProductRepository
     */
    protected $productRepository = null;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository = null;

    /**
     * @var OrderRepository
     */
    protected $orderRepository = null;

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
     * @param OrderRepository $orderRepository
     */
    public function injectOrderRepository(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

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
        $this->createButtons();
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
     */
    public function indexAction()
    {
        $activeOrdersCount = $this->orderRepository
            ->findActive(
                $this->getTreeListArrayForPid($this->pid)
            )
            ->count();
        $this->view->assign('activeOrdersCount', $activeOrdersCount);
    }

    /**
     * Order view
     *
     * @param string $activeTab - Current active tab
     */
    public function listOrdersAction(string $activeTab = '')
    {
        $activeTab = $activeTab ?: $this->settings['listOrders']['tabs']['defaultActive'];

        if ($this->pid > 0) {
            $orderCount = 0;
            $tabsOrders = [];
            $storage = $this->getTreeListArrayForPid($this->pid);
            $tabs = $this->settings['listOrders']['tabs']['list'] ?: [];

            if (!is_array($tabs) || empty($tabs)) {
                $this->addFlashMessage(
                    $this->translate('be.no_tabs'),
                    $this->translate('be.error'),
                    FlashMessage::ERROR
                );
            }

            foreach ($tabs as $tab) {
                $orders = $this->orderRepository->getOrderForTab($tab, $storage);

                $orderCount += $orders->count();
                $tabsOrders[$tab] = $orders;

                if ($activeTab === $tab) {
                    $listOrders = $orders;
                }
            }

            $this->view->assignMultiple([
                'listOrders' => $listOrders ?? [],
                'ordersCount' => $orderCount,
                'tabsOrders' => $tabsOrders,
                'backUrl' => GeneralUtility::getIndpEnv('REQUEST_URI'),
                'activeTab' => $activeTab,
                'pageTitle' => BackendUtility::getRecord('pages', $this->pid, 'title')['title']
            ]);
        }
    }

    /**
     * Show action
     *
     * @param int $order
     * @param string $backUrl
     */
    public function showOrderAction(int $order, string $backUrl = '')
    {
        $order = $this->orderRepository->findByIdIgnoreHidden($order);

        $this->view
            ->assign('totalPrice', ProductUtility::calculateOrderTotalPrice($order, true))
            ->assign('totalTax', ProductUtility::calculateOrderTotalTax($order, true))
            ->assign('backUrl', $backUrl)
            ->assign('order', $order);
    }

    /**
     * Toggle order state like: hidden, complete
     *
     * @param int $order
     * @param string $state
     * @param string $backUrl
     */
    public function toggleOrderStateAction(int $order, string $state, string $backUrl = '')
    {
        $order = $this->orderRepository->findByIdIgnoreHidden($order);
        $currentState = ObjectAccess::getProperty($order, $state);

        ObjectAccess::setProperty($order, $state, !$currentState);

        $this->orderRepository->update($order);

        if (empty($backUrl)) {
            $this->redirect('listOrders');
        } else {
            $this->redirectToUri($backUrl);
        }
    }

    /**
     * delete
     *
     * @param int $order
     * @param string $activeTab
     */
    public function deleteOrderAction(int $order, string $activeTab = 'new')
    {
        $order = $this->orderRepository->findByIdIgnoreHidden($order);
        $this->orderRepository->remove($order);

        $this->redirect('listOrders', null, null, ['activeTab' => $activeTab]);
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
        $products = $this->productRepository->findAllProductsByCategories([$category]);

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
            $products[$category->getUid()] = $this->productRepository->findAllProductsByCategories([$category]);
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

        $uriBuilder = GeneralUtility::makeInstance(BackendUriBuilder::class);

        return (string)$uriBuilder->buildUriFromRoute('record_edit', $urlParameters);
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

    /**
     * Add menu buttons for specific actions
     *
     * @return void
     */
    protected function createButtons()
    {
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        if ($this->view->getModuleTemplate() !== null) {
            $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
            $uriBuilder = $this->objectManager->get(UriBuilder::class);
            $uriBuilder->setRequest($this->request);

            $buttons = [];
            switch ($this->request->getControllerActionName()) {
                case 'listCategories':
                case 'listOrders':
                    $buttons[] = $buttonBar->makeLinkButton()
                        ->setHref($uriBuilder->reset()->uriFor('index'))
                        ->setTitle($this->translate('be.go_back'))
                        ->setIcon($iconFactory->getIcon('actions-view-go-back', Icon::SIZE_SMALL));
                    break;
                case 'showOrder':
                    try {
                        $backUrl = $this->request->getArgument('backUrl');
                    } catch (NoSuchArgumentException $exception) {
                        $backUrl = $uriBuilder->reset()->uriFor('listOrders');
                    }

                    // It might be empty in arguments
                    $backUrl = $backUrl ?: $uriBuilder->reset()->uriFor('listOrders');

                    $buttons[] = $buttonBar->makeLinkButton()
                        ->setHref($backUrl)
                        ->setTitle($this->translate('be.go_back'))
                        ->setIcon($iconFactory->getIcon('actions-view-go-back', Icon::SIZE_SMALL));

                    try {
                        $orderUid = $this->request->getArgument('order');
                        $order = $this->orderRepository->findByIdIgnoreHidden($orderUid);
                        if (!$order->isComplete()) {
                            $buttons[] = $buttonBar->makeLinkButton()
                                ->setHref($uriBuilder->reset()->uriFor('markComplete', ['order' => $order]))
                                ->setTitle($this->translate('be.complete_order'))
                                ->setIcon($iconFactory->getIcon('actions-check', Icon::SIZE_SMALL));
                        }
                    } catch (NoSuchArgumentException $exception) {
                    }
                    break;
            }

            foreach ($buttons as $button) {
                $buttonBar->addButton($button, ButtonBar::BUTTON_POSITION_LEFT);
            }
        }
    }

    /**
     * Get array of recursive pids
     *
     * @param int $pid
     * @return array
     */
    protected function getTreeListArrayForPid(int $pid): array
    {
        $queryGenerator = $this->objectManager->get(QueryGenerator::class);
        return GeneralUtility::intExplode(',', $queryGenerator->getTreeList($pid, 99, 0, 1));
    }
}

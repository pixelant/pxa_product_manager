<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\FilterRepository;
use Pixelant\PxaProductManager\Service\Category\TreeService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * @package Pixelant\PxaProductManager\Controller
 */
class LazyProductController extends AbstractController
{
    use CanCreateCollection;

    /**
     * @var FilterRepository
     */
    protected FilterRepository $filterRepository;

    /**
     * @var TreeService
     */
    protected TreeService $categoryTree;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @param TreeService $treeService
     */
    public function injectTreeService(TreeService $treeService)
    {
        $this->categoryTree = $treeService;
    }

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param FilterRepository $filterRepository
     */
    public function injectFilterRepository(FilterRepository $filterRepository)
    {
        $this->filterRepository = $filterRepository;
    }

    /**
     * App wrapper for lazy loading
     *
     * @return void
     */
    public function listAction()
    {
        // Selected filters
        $filters = $this->findRecordsByList($this->settings['filtering']['filters'], $this->filterRepository);
        // Selected categories
        $categories = $this->findRecordsByList(
            $this->settings['lazyList']['entryCategories'],
            $this->categoryRepository
        );

        $this->view->assign('filters', $filters);
        $this->view->assign('settingsJson', json_encode($this->lazyListSettings($categories)));
    }

    /**
     * Prepare lazy loading settings
     *
     * @param array $categories
     * @return array
     */
    protected function lazyListSettings(array $categories): array
    {
        return [
                'storagePid' => $this->storagePid(),
                'limit' => (int)$this->settings['limit'],
                'filterConjunction' => $this->settings['filtering']['conjunction'],
                'categories' => $this->categoryTree->childrenIdsRecursiveAndCache($categories),
            ] + $this->settings['productOrderings'];
    }

    /**
     * Storages list
     *
     * @return array
     */
    protected function storagePid(): array
    {
        $frameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        return GeneralUtility::intExplode(',', $frameworkConfiguration['persistence']['storagePid'] ?? '');
    }
}

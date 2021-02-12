<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\FilterRepository;
use Pixelant\PxaProductManager\Service\Category\TreeService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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
    public function injectTreeService(TreeService $treeService): void
    {
        $this->categoryTree = $treeService;
    }

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository): void
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param FilterRepository $filterRepository
     */
    public function injectFilterRepository(FilterRepository $filterRepository): void
    {
        $this->filterRepository = $filterRepository;
    }

    /**
     * App wrapper for lazy loading.
     *
     * @return void
     */
    public function listAction(): void
    {
        // Selected filters
        $filters = $this->findRecordsByList($this->settings['filtering']['filters'], $this->filterRepository);

        $this->view->assign('menu', $this->generateMenu());
        $this->view->assign('filters', $filters);
        $this->view->assign('orderBy', json_encode($this->createOrderByArray()));
        $this->view->assign('settingsJson', json_encode($this->lazyListSettings()));
    }

    /**
     * Prepare lazy loading settings.
     *
     * @param array $categories
     * @return array
     */
    protected function lazyListSettings(): array
    {
        if (!empty($this->settings['pageTreeStartingPoint'])) {
            $pageTreeStartingPoint = $this->settings['pageTreeStartingPoint'];
        } else {
            $pageTreeStartingPoint = $this->getTypoScriptFrontendController()->id;
        }

        return [
            'storagePid' => $this->storagePid(),
            'pageTreeStartingPoint' => $pageTreeStartingPoint,
            'limit' => (int)$this->settings['limit'],
            'filterConjunction' => $this->settings['filtering']['conjunction'],
            'hideFilterOptionsNoResult' => (int)$this->settings['filtering']['hideFilterOptionsNoResult'],
        ] + $this->settings['productOrderings'];
    }

    /**
     * Storages list.
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

<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\FilterRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ProductRenderController extends AbstractController
{
    /**
     * @var FilterRepository
     */
    protected FilterRepository $filterRepository;

    /**
     * @param FilterRepository $filterRepository
     */
    public function injectFilterRepository(FilterRepository $filterRepository): void
    {
        $this->filterRepository = $filterRepository;
    }

    /**
     * Init action, forward to proper action.
     *
     * @param Product|null $product
     */
    public function initAction(Product $product = null): void
    {
        if ($product === null) {
            $this->forward('list');
        } else {
            $this->forward(
                'show',
                'ProductRender',
                $this->request->getControllerExtensionName(),
                $this->request->getArguments()
            );
        }
    }

    /**
     * App wrapper for lazy loading.
     *
     * @return void
     */
    public function listAction(): void
    {
        $filters = [];
        $filterIds = $this->settings['filtering']['filters'] ?? [];
        if ($filterIds) {
            $filters = $this->findRecordsByList($this->settings['filtering']['filters'], $this->filterRepository);
        }

        $this->view->assign('filters', $filters);
        $this->view->assign('settingsJson', json_encode($this->lazyListSettings()));
    }

    /**
     * Show product.
     *
     * @param Product $product
     */
    public function showAction(Product $product): void
    {
        $this->view->assignMultiple(compact('product'));
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

        $this->settings['productOrderings'] = [
            'orderBy' => 'name',
            'orderDirection' => 'asc',
        ];

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

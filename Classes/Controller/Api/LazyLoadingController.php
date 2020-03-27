<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller\Api;

use Pixelant\PxaProductManager\Controller\AbstractController;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Mvc\View\LazyLoadingJsonView;
use Pixelant\PxaProductManager\Service\LazyLoading\ProductsQueryDispatcher;
use Pixelant\PxaProductManager\Service\Resource\ResourceConverter;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;

/**
 * @package Pixelant\PxaProductManager\Controller\Api
 */
class LazyLoadingController extends AbstractController
{
    /**
     * @var JsonView
     */
    protected $view;

    /**
     * @var string
     */
    protected $defaultViewObjectName = LazyLoadingJsonView::class;

    /**
     * @var ProductRepository
     */
    protected ProductRepository $productRepository;

    /**
     * @var ResourceConverter
     */
    protected ResourceConverter $resourceConverter;

    /**
     * @var ProductsQueryDispatcher
     */
    protected ProductsQueryDispatcher $productQueryDispatcher;

    /**
     * @param ProductRepository $productRepository
     */
    public function injectProductRepository(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param ResourceConverter $resourceConverter
     */
    public function injectResourceConverter(ResourceConverter $resourceConverter)
    {
        $this->resourceConverter = $resourceConverter;
    }

    /**
     * @param ProductsQueryDispatcher $productQueryDispatcher
     */
    public function injectProductsQueryDispatcher(ProductsQueryDispatcher $productQueryDispatcher)
    {
        $this->productQueryDispatcher = $productQueryDispatcher;
    }

    /**
     * Initialize configuration
     */
    public function initializeListAction()
    {
        // allow to create Demand from arguments
        $allowedProperties = GeneralUtility::trimExplode(
            ',',
            $this->settings['demand']['allowMappingProperties']
        );

        /** @var PropertyMappingConfiguration $demandConfiguration */
        $demandConfiguration = $this->arguments['demand']->getPropertyMappingConfiguration();
        $demandConfiguration->allowProperties(...$allowedProperties);
    }

    /**
     * Lazy list loading
     *
     * @param ProductDemand $demand
     */
    public function listAction(ProductDemand $demand)
    {
        $products = $this->productRepository->findDemanded($demand)->toArray();
        $this->productQueryDispatcher->prepareQuery($demand);

        $countAll = null;
        $availableOptions = null;

        // Do additional operations only if this is first loading - when offset is not set
        if ($demand->getOffSet() === 0) {
            $countAll = $demand->getLimit() ? $this->productQueryDispatcher->countAll() : count($products);
            if ($demand->isHideFilterOptionsNoResult()) {
                $availableOptions = $this->productQueryDispatcher->availableFilterOptions();
            }
        }

        $response = [
            'products' => $this->resourceConverter->covertMany($products),
            'countAll' => $countAll,
            'availableFilterOptions' => $availableOptions,
        ];

        $this->view->setVariablesToRender(['response']);
        $this->view->assign('response', $response);
    }
}

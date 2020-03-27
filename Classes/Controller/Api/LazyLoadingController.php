<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller\Api;

use Pixelant\PxaProductManager\Controller\AbstractController;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Mvc\View\LazyLoadingJsonView;
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
        $products = $this->productRepository->findDemanded($demand);

        $response = [
            'products' => $this->resourceConverter->covertMany($products->toArray()),
        ];

        $this->view->setVariablesToRender(['response']);
        $this->view->assign('response', $response);
    }
}

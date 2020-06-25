<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller\Api;

use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Service\Resource\ResourceConverter;

/**
 * @package Pixelant\PxaProductManager\Controller\Api
 */
class LazyLoadingController extends AbstractBaseLazyLoadingController
{
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
     * Lazy list loading
     *
     * @param ProductDemand $demand
     */
    public function listAction(ProductDemand $demand)
    {
        $demand->setOrderByAllowed($this->settings['demand']['orderByAllowed'] ?? '');
        $products = $this->productRepository->findDemanded($demand)->toArray();

        $response = [
            'products' => $this->resourceConverter->covertMany($products),
        ];

        $this->view->setVariablesToRender(['response']);
        $this->view->assign('response', $response);
    }
}

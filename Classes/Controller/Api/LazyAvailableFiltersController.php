<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller\Api;

use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use Pixelant\PxaProductManager\Domain\Model\Filter;
use Pixelant\PxaProductManager\Service\LazyLoading\ProductsQueryDispatcher;

class LazyAvailableFiltersController extends AbstractBaseLazyLoadingController
{
    /**
     * @var ProductsQueryDispatcher
     */
    protected ProductsQueryDispatcher $queryDispatcher;

    /**
     * @param ProductsQueryDispatcher $productsQueryDispatcher
     */
    public function injectProductsQueryDispatcher(ProductsQueryDispatcher $productsQueryDispatcher): void
    {
        $this->queryDispatcher = $productsQueryDispatcher;
    }

    /**
     * Count all results for demand and return available filter options.
     *
     * @param ProductDemand $demand
     */
    public function listAction(ProductDemand $demand): void
    {
        $this->queryDispatcher->prepareQuery($demand);

        // Count results
        $response = [
            'countAll' => $this->queryDispatcher->countAll(),
        ];

        $response['options'] = $this->collectAvailableOptions($demand);

        $this->view->setVariablesToRender(['response']);
        $this->view->assign('response', $response);
    }

    /**
     * Available filter options for demand.
     *
     * @param ProductDemand $demand
     * @return array|null
     */
    protected function collectAvailableOptions(ProductDemand $demand): ?array
    {
        // Do nothing if options is disable in plugin settings
        if (!$demand->isHideFilterOptionsNoResult()) {
            return null;
        }

        // Available filter options for all AND filters
        $options = [
            // For all AND filters
            'and' => array_merge(
                $this->queryDispatcher->availableFilterOptions(),
                $this->queryDispatcher->availableCategories(),
            ),
        ];

        // For each OR filter we need to get available options without this filter set
        $orFilters = array_filter($demand->getFilters(), fn ($fd) => $fd['conjunction'] === Filter::CONJUNCTION_OR);
        foreach ($orFilters as $id => $data) {
            $customDemand = clone $demand;
            $customDemand->removeFilter($id);

            $this->queryDispatcher->prepareQuery($customDemand);
            $options[$id] = (int) ($data['type']) === Filter::TYPE_CATEGORIES
                ? $this->queryDispatcher->availableCategories()
                : $this->queryDispatcher->availableFilterOptions();
        }

        return $options;
    }
}

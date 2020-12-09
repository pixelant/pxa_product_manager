<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service\LazyLoading;

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ProductsQueryDispatcher
{
    /**
     * @var AttributeValueRepository
     */
    protected AttributeValueRepository $attributeValueRepository;

    /**
     * @var QueryBuilder
     */
    protected QueryBuilder $queryBuilder;

    /**
     * @var ProductRepository
     */
    protected ProductRepository $productRepository;

    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $categoryRepository;

    /**
     * @param AttributeValueRepository $attributeValueRepository
     */
    public function injectAttributeValueRepository(AttributeValueRepository $attributeValueRepository): void
    {
        $this->attributeValueRepository = $attributeValueRepository;
    }

    /**
     * @param ProductRepository $productRepository
     */
    public function injectProductRepository(ProductRepository $productRepository): void
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository): void
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Prepare extbase query for further processing.
     *
     * @param DemandInterface $demand
     */
    public function prepareQuery(DemandInterface $demand): void
    {
        $demand = clone $demand;
        $demand->setLimit(0);
        $demand->setOffSet(0);

        $this->queryBuilder = $this->productRepository->createDemandQueryBuilder($demand);
    }

    /**
     * Return all available options for current products query builder.
     *
     * @return array
     */
    public function availableFilterOptions(): array
    {
        $options = $this->attributeValueRepository->findOptionIdsByProductSubQuery($this->subQuery());

        return array_unique(array_merge(
            ...array_map(fn ($value) => GeneralUtility::intExplode(',', $value, true), $options)
        ));
    }

    /**
     * Return all available categories for query builder.
     * @return array
     */
    public function availableCategories(): array
    {
        return $this->categoryRepository->findIdsByProductsSubQuery($this->subQuery());
    }

    /**
     * Count all results for products demand query.
     *
     * @return int
     */
    public function countAll(): int
    {
        $queryBuilder = clone $this->queryBuilder;

        return (int)$queryBuilder
            ->count('tx_pxaproductmanager_domain_model_product.uid')
            ->execute()
            ->fetchColumn(0);
    }

    /**
     * Generate products sub-query string.
     *
     * @return string
     */
    protected function subQuery(): string
    {
        $queryBuilder = clone $this->queryBuilder;
        $queryBuilder->select('tx_pxaproductmanager_domain_model_product.uid');

        return $this->queryBuilderToSql($queryBuilder);
    }

    /**
     * Convert query builder to sql.
     *
     * @param QueryBuilder $queryBuilder
     * @return string
     */
    protected function queryBuilderToSql(QueryBuilder $queryBuilder): string
    {
        $sql = $queryBuilder->getSQL();
        $parameters = $queryBuilder->getParameters();
        foreach ($parameters as $key => $parameter) {
            switch ($queryBuilder->getParameterType($key)) {
                case 1:
                    $stringParams[':' . $key] = (int)$parameter;

                    break;
                case 101:
                    $stringParams[':' . $key] = implode(',', $parameter);

                    break;
                default:
                    $stringParams[':' . $key] = $queryBuilder->quote($parameter);

                    break;
            }
        }
        $statement = strtr($sql, $stringParams);

        return $statement;
    }
}

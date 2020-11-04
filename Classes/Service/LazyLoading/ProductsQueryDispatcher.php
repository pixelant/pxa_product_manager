<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service\LazyLoading;

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;

class ProductsQueryDispatcher
{
    /**
     * @var AttributeValueRepository
     */
    protected AttributeValueRepository $attributeValueRepository;

    /**
     * @var Typo3DbQueryParser
     */
    protected Typo3DbQueryParser $queryParser;

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
     * @param Typo3DbQueryParser $typo3DbQueryParser
     */
    public function injectTypo3DbQueryParser(Typo3DbQueryParser $typo3DbQueryParser): void
    {
        $this->queryParser = $typo3DbQueryParser;
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

        $this->queryBuilder = $this->queryParser->convertQueryToDoctrineQueryBuilder(
            $this->productRepository->createDemandQuery($demand)
        );
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
        $queryParameters = [];

        foreach ($queryBuilder->getParameters() as $key => $value) {
            // prefix array keys with ':'
            //all non numeric values have to be quoted
            $queryParameters[':' . $key] = is_numeric($value) ? $value : "'${value}'";
        }

        return strtr($queryBuilder->getSQL(), $queryParameters);
    }
}

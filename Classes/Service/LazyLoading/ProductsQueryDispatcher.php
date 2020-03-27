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

/**
 * @package Pixelant\PxaProductManager\Service\LazyLoading
 */
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
    public function injectAttributeValueRepository(AttributeValueRepository $attributeValueRepository)
    {
        $this->attributeValueRepository = $attributeValueRepository;
    }

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
     * @param Typo3DbQueryParser $typo3DbQueryParser
     */
    public function injectTypo3DbQueryParser(Typo3DbQueryParser $typo3DbQueryParser)
    {
        $this->queryParser = $typo3DbQueryParser;
    }

    /**
     * Prepare extbase query for further processing
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
     * Return all available options for current products query builder
     *
     * @return array
     */
    public function availableFilterOptions(): array
    {
        $queryBuilder = clone $this->queryBuilder;
        $queryBuilder->select('tx_pxaproductmanager_domain_model_product.uid');

        $subQuery = $this->queryBuilderToSql($queryBuilder);

        $options = $this->attributeValueRepository->findOptionIdsByProductSubQuery($subQuery);
        $options = array_unique(array_merge(
            ...array_map(fn($value) => GeneralUtility::intExplode(',', $value, true), $options)
        ));

        $categories = $this->categoryRepository->findIdsByProductsSubQuery($subQuery);

        return compact('options', 'categories');
    }

    /**
     * Count all results for products demand query
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
     * Convert query builder to sql
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
            $queryParameters[':' . $key] = is_numeric($value) ? $value : "'$value'";
        }

        return strtr($queryBuilder->getSQL(), $queryParameters);
    }
}

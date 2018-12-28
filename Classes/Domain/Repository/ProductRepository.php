<?php

namespace Pixelant\PxaProductManager\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic\Query;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\Demand;
use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Model\Filter;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Traits\ProcessQueryResultEntitiesTrait;
use Pixelant\PxaProductManager\Utility\TCAUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Extbase\Object\Container\Container;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 *
 *
 * @package pxa_product_manager
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class ProductRepository extends AbstractDemandRepository
{
    use ProcessQueryResultEntitiesTrait;

    /**
     * @var Container
     */
    protected $container = null;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository = null;


    /**
     * Special ordering field that require additinal fixes
     *
     * @var string
     */
    protected $categoriesSortingField = 'categories.sorting';

    /**
     * @param Container $container
     */
    public function injectContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Override basic method. Set special ordering for categories if it's not multiple
     *
     * @param DemandInterface|Demand $demand
     * @return QueryResultInterface
     */
    public function findDemanded(DemandInterface $demand): QueryResultInterface
    {
        return $this->findDemandedCommon($demand);
    }

    /**
     * @param DemandInterface|Demand $demand
     * @return array
     */
    public function findDemandedRaw(DemandInterface $demand): array
    {
        return $this->findDemandedCommon($demand, true);
    }

    /**
     * Count results for demand
     *
     * @param DemandInterface $demand
     * @return int
     */
    public function countByDemand(DemandInterface $demand): int
    {
        return $this->findDemanded($demand)->count();
    }

    /**
     * If order is by category need to override basic order function
     *
     * @param QueryInterface $query
     * @param DemandInterface|Demand $demand
     */
    public function setOrderings(QueryInterface $query, DemandInterface $demand)
    {
        parent::setOrderings($query, $demand);

        $orderings = $query->getOrderings();
        // Include name as second sorting if not already chosen
        if (!array_key_exists('name', $orderings)) {
            $orderings['name'] = QueryInterface::ORDER_ASCENDING;

            $query->setOrderings($orderings);
        }
    }

    /**
     * Find all product with storage or all
     *
     * @param bool $respectStorage
     * @return QueryResultInterface
     */
    public function findAll($respectStorage = true)
    {
        $query = $this->createQuery();

        if (!$respectStorage) {
            $query->getQuerySettings()->setRespectStoragePage(false);
        }

        return $query->execute();
    }

    /**
     * Find products by categories
     *
     * @param array $categories
     * @param bool $showHidden
     * @param array $orderings
     * @param string $conjunction
     * @param int $limit
     * @return array|QueryResultInterface
     */
    public function findProductsByCategories(
        array $categories,
        bool $showHidden = false,
        array $orderings = ['sorting' => QueryInterface::ORDER_ASCENDING],
        string $conjunction = 'and',
        int $limit = 0
    ) {
        if (empty($categories)) {
            return [];
        }

        // Find products our own way, because CategoryCollection::load doesn't have options to set the ordering
        $query = $this->createQuery();
        if (true === $showHidden) {
            $query
                ->getQuerySettings()
                ->setIgnoreEnableFields(true)
                ->setEnableFieldsToBeIgnored(['disabled']);
        }

        $constraints = [];
        /** @var Category $category */
        foreach ($categories as $category) {
            $constraints[] = $query->contains('categories', $category);
        }

        $query->matching(
            $this->createConstraintFromConstraintsArray(
                $query,
                $constraints,
                $conjunction
            )
        );

        $query->setOrderings($orderings);

        // Set limit
        if ($limit > 0) {
            $query->setLimit($limit);
        }

        return $query->execute();
    }

    /**
     * Count products for category
     *
     * @param Category $category
     * @return int
     */
    public function countByCategory(Category $category): int
    {
        $query = $this->createQuery();

        $query->matching(
            $query->contains('categories', $category)
        );

        return $query->count();
    }

    /**
     * findProductsByUIds
     *
     * @param array $uids
     * @return QueryResultInterface|array
     */
    public function findProductsByUids(array $uids = [])
    {
        if (empty($uids)) {
            return [];
        }

        $query = $this->createQuery();

        // Disable language and storage check, because we are using uids
        $query
            ->getQuerySettings()
            ->setRespectSysLanguage(false)
            ->setRespectStoragePage(false);

        $query->matching(
            $query->in('uid', $uids)
        );

        return $query->execute();
    }

    /**
     * Add possibility do disable enable fields when find by uid
     *
     * @param int $uid
     * @param bool $respectEnableFields
     * @return null|Product
     */
    public function findByUid($uid, bool $respectEnableFields = true)
    {
        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->getQuerySettings()->setRespectStoragePage(false);

        if (false === $respectEnableFields) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);
        }

        $query->matching(
            $query->equals('uid', (int)$uid)
        );

        return $query->execute()->getFirst();
    }

    /**
     * Convert query to SQL
     * Own method with usage of own query parser
     *
     * @see removeDuplicatedEntries
     * @param $query
     * @return string
     */
    public function convertQueryBuilderToSql(QueryInterface $query): string
    {
        $queryParser = $this->objectManager->get(Typo3DbQueryParser::class);
        $queryBuilder = $queryParser->convertQueryToDoctrineQueryBuilder($query);

        $selectParts = $queryBuilder->getQueryPart('select');

        if ($queryParser->isDistinctQuerySuggested() && !empty($selectParts)) {
            $selectParts[0] = 'DISTINCT ' . $selectParts[0];
            // Need to add 'sys_category.sorting' with DISTINCT in order to don't get SQL error in strict mode
            // Fix duplicate records in @see removeDuplicatedEntries
            if ($this->isQueryWithCategoriesSorting($query)) {
                $selectParts[] = $queryBuilder->quoteIdentifier('sys_category.sorting');
            }
            $queryBuilder->selectLiteral(...$selectParts);
        }
        if ($query->getOffset()) {
            $queryBuilder->setFirstResult($query->getOffset());
        }
        if ($query->getLimit()) {
            $queryBuilder->setMaxResults($query->getLimit());
        }

        $queryParameters = [];

        foreach ($queryBuilder->getParameters() as $key => $value) {
            // prefix array keys with ':'
            //all non numeric values have to be quoted
            $queryParameters[':' . $key] = is_numeric($value)
                ? $value
                : $queryBuilder->quote($value, \PDO::PARAM_STR);
        }

        return strtr($queryBuilder->getSQL(), $queryParameters);
    }

    /**
     * Create own query object
     *
     * @return Query|QueryInterface
     */
    public function createQuery()
    {
        // Backup class name
        $queryClassName = $this->container->getImplementationClassName(QueryInterface::class);
        // Set our own query class name
        $this->container->registerImplementation(QueryInterface::class, Query::class);
        // Create query
        $query = parent::createQuery();
        // Reset changes
        $this->container->registerImplementation(QueryInterface::class, $queryClassName);

        return $query;
    }

    /**
     * Create constraints for all demand options
     *
     * @param QueryInterface $query
     * @param DemandInterface|Demand $demand
     * @return array
     */
    protected function createConstraints(QueryInterface $query, DemandInterface $demand): array
    {
        $constraints = [];

        if (!$demand->getIncludeDiscontinued()) {
            $constraints['discontinued'] = $this->createDiscontinuedConstraints($query);
        }

        if (!empty($demand->getCategories())) {
            $constraints['categories'] = $this->createCategoryConstraints(
                $query,
                $demand->getCategories(),
                $demand->getCategoryConjunction()
            );
        }

        if (!empty($demand->getFilters())) {
            $filterConstraints = $this->createFilteringConstraints(
                $query,
                $demand->getFilters(),
                $demand->getFiltersConjunction()
            );
            if ($filterConstraints !== false) {
                $constraints['filters'] = $filterConstraints;
            }
        }

        return $constraints;
    }

    /**
     * Filters
     * Filters are generated on FE in ProductManager.Filtering.js
     * Array
     * (
     *  [2-13] => Array // type + uid of filter
     * (
     *  [type] => 2 // type of filter
     *  [attributeUid] => 13 // UID of attribute or parent category
     *  [value] => Array // array of values
     * (
     *      [0] => 3
     *  )
     * )
     * @param Query|QueryInterface $query
     * @param array $filters
     * @param string $conjunction
     * @return mixed
     */
    protected function createFilteringConstraints(QueryInterface $query, array $filters, string $conjunction = 'or')
    {
        $constraints = [];
        $attributeValuesPropertyName = GeneralUtility::underscoredToLowerCamelCase(
            TCAUtility::ATTRIBUTES_VALUES_FIELD_NAME
        );

        $ranges = [];

        foreach ($filters as $filter) {
            if (!empty($filter['value'])) {
                switch ((int)$filter['type']) {
                    case Filter::TYPE_ATTRIBUTES:
                        $filterConstraints = [];
                        foreach ($filter['value'] as $value) {
                            $filterConstraints[] = $query->contains(
                                $attributeValuesPropertyName . '->' . $filter['attributeUid'],
                                $value
                            );
                        }
                        if (!empty($filterConstraints)) {
                            $constraints[] = $this->createConstraintFromConstraintsArray(
                                $query,
                                $filterConstraints,
                                'or'
                            );
                        }
                        break;
                    case Filter::TYPE_CATEGORIES:
                        $categoriesConstraints = [];
                        foreach ($filter['value'] as $value) {
                            $categoriesConstraints[] = $query->contains('categories', $value);
                        }

                        $constraints[] = $this->createConstraintFromConstraintsArray(
                            $query,
                            $categoriesConstraints,
                            'or'
                        );
                        break;
                    case Filter::TYPE_ATTRIBUTES_MINMAX:
                        // need to just prebuild array since minmax attribute filter can consist of two inputs
                        list($value, $rangeType) = $filter['value'];
                        $rangeKey = (int)$filter['attributeUid'];

                        $ranges[$rangeKey][$rangeType] = $value;
                        break;
                    default:
                        // @codingStandardsIgnoreStart
                        throw new \UnexpectedValueException('Filter type "' . $filter['type'] . '" is not supported.', 1545920531427);
                    // @codingStandardsIgnoreEnd
                }
            }
        }

        // go through ranges after all filters have been processed
        // since they can have value from two filter inputs
        if (!empty($ranges)) {
            foreach ($ranges as $attributeId => $range) {
                $constraints[] = $query->attributesRange(
                    $attributeValuesPropertyName . '->' . $filter['attributeUid'],
                    isset($range['min']) ? (int)$range['min'] : null,
                    isset($range['max']) ? (int)$range['max'] : null
                );
            }
        }

        if (!empty($constraints)) {
            return $this->createConstraintFromConstraintsArray(
                $query,
                $constraints,
                strtolower($conjunction)
            );
        }

        return false;
    }

    /**
     * Create categories constraints
     *
     * @param QueryInterface $query
     * @param array $categories
     * @param string $conjunction
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface
     */
    protected function createCategoryConstraints(QueryInterface $query, array $categories, string $conjunction = 'or')
    {
        $constraints = [];

        foreach ($categories as $category) {
            $constraints[] = $query->contains('categories', $category);
        }

        return $this->createConstraintFromConstraintsArray(
            $query,
            $constraints,
            strtolower($conjunction)
        );
    }

    /**
     * Create discontinued constraints
     *
     * @param QueryInterface $query
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface
     */
    protected function createDiscontinuedConstraints(QueryInterface $query)
    {
        $constraints = [];

        // include if discontinued isn't set
        $constraints['ns'] = $query->equals('discontinued', 0);
        // or discontinued is greater than today
        $constraints['gt'] = $query->greaterThan('discontinued', new \DateTime('00:00'));

        return $this->createConstraintFromConstraintsArray(
            $query,
            $constraints,
            'or'
        );
    }

    /**
     * Wrap same actions for find demanded raw and not raw.
     * This include required fixes for categories sorting and JSON attributes_values support
     *
     * @param Demand $demand
     * @param bool $returnRawResult
     * @return array|QueryResultInterface
     */
    protected function findDemandedCommon(Demand $demand, bool $returnRawResult = false)
    {
        $query = $this->createDemandQuery($demand);
        $sql = $this->convertQueryBuilderToSql($query);

        $queryResults = $query->statement($sql)->execute($returnRawResult);

        if ($this->isQueryWithCategoriesSorting($query)) {
            // If sorting by 'categories.sorting' DISTINCT won't work
            // @TODO how to fix DISTINCT
            $queryResults = $this->removeDuplicatedEntries($queryResults);

            // If only one category in demand sort by MM order
            if (count($demand->getCategories()) === 1) {
                $productsOrdering = $this->categoryRepository->getProductsOrderingByCategory(
                    reset($demand->getCategories())
                );

                $orderings = $query->getOrderings();
                $queryResults = $this->sortEntitiesAccordingToList(
                    $queryResults,
                    $productsOrdering,
                    'uid',
                    $orderings[$this->categoriesSortingField] === QueryInterface::ORDER_DESCENDING
                );
            }
        }

        return $queryResults;
    }

    /**
     * Check if query has categories sorting
     *
     * @param QueryInterface $query
     * @return bool
     */
    protected function isQueryWithCategoriesSorting(QueryInterface $query): bool
    {
        return array_key_exists($this->categoriesSortingField, $query->getOrderings());
    }
}

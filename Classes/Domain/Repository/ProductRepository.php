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
use Pixelant\PxaProductManager\Utility\TCAUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
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
    /**
     * @var Container
     */
    protected $container = null;

    /**
     * @param Container $container
     */
    public function injectContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Override basic method. Set special ordering for categories if it's not multiple
     *
     * @param DemandInterface|Demand $demand
     * @return QueryResultInterface
     */
    public function findDemanded(DemandInterface $demand): QueryResultInterface
    {
        $query = $this->createDemandQuery($demand);
        $sql = $this->convertQueryBuilderToSql($query);

        return $query->statement($sql)->execute();

        if (false || ($demand->getOrderBy() !== 'categories' || count($demand->getCategories()) > 1)) {
            return parent::findDemanded($demand);
        } else {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable('sys_category_record_mm');
            $queryBuilder->getRestrictions()->removeAll();

            $statement = $queryBuilder
                ->select('uid_foreign')
                ->from('sys_category_record_mm')
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid_local',
                        $queryBuilder->createNamedParameter(
                            $demand->getCategories()[0],
                            Connection::PARAM_INT
                        )
                    ),
                    $queryBuilder->expr()->eq(
                        'tablenames',
                        $queryBuilder->createNamedParameter(
                            'tx_pxaproductmanager_domain_model_product',
                            Connection::PARAM_STR
                        )
                    ),
                    $queryBuilder->expr()->eq(
                        'fieldname',
                        $queryBuilder->createNamedParameter(
                            'categories',
                            Connection::PARAM_STR
                        )
                    )
                )
                ->orderBy('sorting')
                ->execute();

            $uidsOrder = '';
            while ($uid = $statement->fetchColumn(0)) {
                $uidsOrder .= ',' . $uid;
            }
            unset($statement);

            if (empty($uidsOrder)) {
                return parent::findDemanded($demand);
            } else {
                // If sorting is set to categories and we have one category
                $query = $this->createDemandQuery($demand);
                /** @var Typo3DbQueryParser $queryParser */
                $queryParser = $this->objectManager->get(Typo3DbQueryParser::class);

                $productsQueryBuilder = $queryParser->convertQueryToDoctrineQueryBuilder($query);

                // add orderings
                $productsQueryBuilder->add(
                    'orderBy',
                    'FIELD(`tx_pxaproductmanager_domain_model_product`.`uid`' . $uidsOrder . ') '
                    . $demand->getOrderDirection()
                );

                $queryParameters = [];

                foreach ($productsQueryBuilder->getParameters() as $key => $value) {
                    // prefix array keys with ':'
                    //all non numeric values have to be quoted
                    $queryParameters[':' . $key] = (is_numeric($value)) ? $value : "'" . $value . "'";
                }

                $statement = strtr($productsQueryBuilder->getSQL(), $queryParameters);

                return $query->statement($statement)->execute();
            }
        }
    }

    /**
     * @param DemandInterface $demand
     * @return array
     */
    public function findDemandedRaw(DemandInterface $demand): array
    {
        $query = $this->createDemandQuery($demand);
        $sql = $this->convertQueryBuilderToSql($query);

        return $query->statement($sql)->execute(true);
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
        // If sorting is set by categories, we need to create a special query
        if ($demand->getOrderBy() !== 'categories') {
            parent::setOrderings($query, $demand);

            $orderings = $query->getOrderings();
            // Include name as second sorting if not already chosen
            if (!array_key_exists('name', $orderings)) {
                $orderings['name'] = QueryInterface::ORDER_ASCENDING;

                $query->setOrderings($orderings);
            }
        } else {
            $demand->setOrderBy('categories.sorting');
            parent::setOrderings($query, $demand);
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
}

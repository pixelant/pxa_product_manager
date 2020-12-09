<?php

namespace Pixelant\PxaProductManager\Domain\Repository;

/*
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
 */

use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use Pixelant\PxaProductManager\Domain\Model\Filter;
use Pixelant\PxaProductManager\Event\Repository\FilterConstraints;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ProductRepository extends AbstractDemandRepository
{
    use AbleFindByUidList;

    public function getObjectClassName(): string
    {
        return \Pixelant\PxaProductManager\Domain\Model\Product::class;
    }

    /**
     * Create Demand QueryBuilder.
     *
     * @param DemandInterface $demand
     * @return QueryBuilder
     */
    public function createDemandQueryBuilder(DemandInterface $demand): QueryBuilder
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_pxaproductmanager_domain_model_product');

        $queryBuilder
            ->select(
                'tx_pxaproductmanager_domain_model_product.uid',
                'tx_pxaproductmanager_domain_model_product.name',
                'tx_pxaproductmanager_domain_model_product.sku',
                'tx_pxaproductmanager_domain_model_product.price',
                'tx_pxaproductmanager_domain_model_product.singleview_page',
                'tx_pxaproductmanager_domain_model_product.images',
            )
            ->from('tx_pxaproductmanager_domain_model_product');

        $this->fireDemandEvent('afterDemandQueryBuilderInitialize', $demand, $queryBuilder);

        $this->addStorageExpression($queryBuilder, $demand);

        $this->addProductPagesExpression($queryBuilder, $demand);

        $this->fireDemandEvent('beforeDemandQueryBuilderFilters', $demand, $queryBuilder);

        $this->addFilters($queryBuilder, $demand);

        $this->fireDemandEvent('afterDemandQueryBuilderFilters', $demand, $queryBuilder);

        $this->addLimit($queryBuilder, $demand);

        $this->addOffset($queryBuilder, $demand);

        $this->addOrderings($queryBuilder, $demand);

        $this->fireDemandEvent('afterDemandQueryBuilder', $demand, $queryBuilder);

        return $queryBuilder;
    }

    /**
     * Add product pages expression if set.
     *
     * @param QueryBuilder $queryBuilder
     * @param DemandInterface $demand
     */
    protected function addProductPagesExpression(QueryBuilder $queryBuilder, DemandInterface $demand): void
    {
        $pageTreeStartingPoint = $demand->getPageTreeStartingPoint();
        if ($pageTreeStartingPoint) {
            $pageRepository = $this->objectManager->get(PageRepository::class);
            $childPages = $pageRepository->getMenu($pageTreeStartingPoint);
            $childUids = array_keys($childPages);
            $singleViewPages = array_merge([$pageTreeStartingPoint], $childUids);

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(
                    'uid',
                    $this->getProductPagesSubQuery($singleViewPages, $queryBuilder)
                )
            );
        }
    }

    protected function getProductPagesSubQuery($singleViewPageIds, $parentQueryBuilder)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');

        return $queryBuilder->select('tpppm.uid_local')
            ->from('pages')
            ->join(
                'pages',
                'tx_pxaproductmanager_product_pages_mm',
                'tpppm',
                $queryBuilder->expr()->eq(
                    'tpppm.uid_foreign',
                    $queryBuilder->quoteIdentifier('pages.uid')
                ) .
                ' AND tpppm.tablenames = \'pages\'' .
                ' AND tpppm.fieldname = \'doktype\''
            )
            ->where(
                $queryBuilder->expr()->in(
                    'pages.uid',
                    $parentQueryBuilder->createNamedParameter(
                        $singleViewPageIds,
                        \TYPO3\CMS\Core\Database\Connection::PARAM_INT_ARRAY
                    )
                )
            )
            ->groupBy('tpppm.uid_local')
            ->getSQL();
    }

    protected function getAttributeSubQuery($attributeId, $values, $parentQueryBuilder, $conjunction)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_pxaproductmanager_domain_model_attribute');

        $expressionBuilder = $queryBuilder->expr();

        if ($conjunction === 'and') {
            $function = 'andX';
        } else {
            $function = 'orX';
        }

        $conditions = $expressionBuilder->{$function}();

        foreach ($values as $value) {
            $conditions->add(
                $expressionBuilder->like(
                    'tpdmav.value',
                    $parentQueryBuilder->createNamedParameter(
                        '%,' . $queryBuilder->escapeLikeWildcards($value) . ',%'
                    )
                )
            );
        }

        $subQuery = $queryBuilder->select('tpdmav.product')
            ->from('tx_pxaproductmanager_domain_model_attribute', 'tpdma')
            ->join(
                'tpdma',
                'tx_pxaproductmanager_domain_model_attributevalue',
                'tpdmav',
                $queryBuilder->expr()->eq(
                    'tpdmav.attribute',
                    $queryBuilder->quoteIdentifier('tpdma.uid')
                )
            )
            ->where(
                $queryBuilder->expr()->eq(
                    'tpdma.uid',
                    $parentQueryBuilder->createNamedParameter($attributeId, \PDO::PARAM_INT)
                ),
            )
            ->andWhere($conditions)
            ->groupBy('tpppm.uid_local');

        return $subQuery->getSQL();
    }

    protected function getCategoriesSubQuery($categoryId, $values, $parentQueryBuilder, $conjunction)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_category');

        $expressionBuilder = $queryBuilder->expr();

        if ($conjunction === 'and') {
            $function = 'andX';
        } else {
            $function = 'orX';
        }

        $conditions = $expressionBuilder->{$function}();

        foreach ($values as $value) {
            $conditions->add(
                $expressionBuilder->eq(
                    'sc.uid',
                    $parentQueryBuilder->createNamedParameter((int)$value, \PDO::PARAM_INT)
                )
            );
        }

        $subQuery = $queryBuilder->select('scrm.uid_foreign')
            ->from('sys_category', 'sc')
            ->join(
                'sc',
                'sys_category_record_mm',
                'scrm',
                $queryBuilder->expr()->eq(
                    'scrm.uid_local',
                    $queryBuilder->quoteIdentifier('sc.uid')
                )
            )
            ->where(
                $conditions
            )
            ->andWhere(
                $queryBuilder->expr()->eq(
                    'scrm.tablenames',
                    $parentQueryBuilder->createNamedParameter(
                        'tx_pxaproductmanager_domain_model_product',
                        \PDO::PARAM_STR
                    )
                ),
                $queryBuilder->expr()->eq(
                    'scrm.fieldname',
                    $parentQueryBuilder->createNamedParameter(
                        'categories',
                        \PDO::PARAM_STR
                    )
                )
            )
            ->groupBy('tpppm.uid_local');

        return $subQuery->getSQL();
    }

    protected function addFilters(QueryBuilder $queryBuilder, DemandInterface $demand): void
    {
        $filters = $demand->getFilters();
        if (empty($filters)) {
            return;
        }

        $conjunction = $demand->getFilterConjunction();

        if ($conjunction === 'and') {
            $function = 'andX';
        } else {
            $function = 'orX';
        }

        $expressionBuilder = $queryBuilder->expr();

        $conditions = $expressionBuilder->{$function}();

        foreach ($filters as $filterData) {
            $type = (int)$filterData['type'];
            $conjunction = $filterData['conjunction'];
            $value = $filterData['value'];

            if ($type === Filter::TYPE_CATEGORIES) {
                $conditions->add(
                    $queryBuilder->expr()->in(
                        'uid',
                        $this->getCategoriesSubQuery((int)$filterData['attribute'], $value, $queryBuilder, $conjunction)
                    )
                );
            } elseif ($type === Filter::TYPE_ATTRIBUTES) {
                $conditions->add(
                    $queryBuilder->expr()->in(
                        'uid',
                        $this->getAttributeSubQuery((int)$filterData['attribute'], $value, $queryBuilder, $conjunction)
                    )
                );
            }
        }

        $queryBuilder->andWhere($conditions);
    }

    /**
     * @param QueryInterface $query
     * @param ProductDemand|DemandInterface $demand
     * @return array
     */
    protected function createConstraints(QueryInterface $query, DemandInterface $demand): array
    {
        $constraints = [];

        // In case demand has category filter, skip setting categories constraint,
        // since filter already has it's own categories restriction
        if (!empty($demand->getCategories()) && !$demand->hasFiltersCategoryFilter()) {
            $constraints['categories'] = $this->categoriesConstraint($query, $demand);
        }

        // If filters are present in demand it mean this is lazy loading request.
        // Categories will be set as filter constraint.
        // Attributes and categories are sharing same conjunction filters settings.
        if (!empty($demand->getFilters())) {
            $constraints['filters'] = $this->filtersConstraint($query, $demand);
        }

        return $constraints;
    }

    /**
     * Create categories constraint from demand.
     *
     * @param QueryInterface $query
     * @param DemandInterface|ProductDemand $demand
     * @return ConstraintInterface
     */
    protected function categoriesConstraint(QueryInterface $query, DemandInterface $demand): ConstraintInterface
    {
        // If OR, just use in query, reduce number of joins
        // Or is always used for entry point demand of list/lazy loading
        if ($this->isOrConjunction($demand->getCategoryConjunction())) {
            return $query->in('categories.uid', $demand->getCategories());
        }

        return $this->createConstraintFromConstraintsArray(
            $query,
            $this->categoriesContainsConstraints($query, $demand->getCategories()),
            $demand->getCategoryConjunction()
        );
    }

    /**
     * Create categories constraints array.
     *
     * @param QueryInterface $query
     * @param array $categories
     * @return array
     */
    protected function categoriesContainsConstraints(QueryInterface $query, array $categories): array
    {
        $constraints = [];
        foreach ($categories as $category) {
            $constraints[] = $query->contains('categories', $category);
        }

        return $constraints;
    }

    /**
     * Create filters constraint.
     *
     * @param QueryInterface $query
     * @param ProductDemand|DemandInterface $demand
     * @return ConstraintInterface
     */
    protected function filtersConstraint(QueryInterface $query, DemandInterface $demand): ConstraintInterface
    {
        $constraints = [];

        foreach ($demand->getFilters() as $filterData) {
            $type = (int)$filterData['type'];
            $conjunction = $filterData['conjunction'];
            $value = $filterData['value'];

            if ($type === Filter::TYPE_CATEGORIES) {
                $constraints[] = $this->createConstraintFromConstraintsArray(
                    $query,
                    $this->categoriesContainsConstraints($query, $value),
                    $conjunction
                );
            } elseif ($type === Filter::TYPE_ATTRIBUTES) {
                $constraints[] = $this->attributeFilterConstraint(
                    $query,
                    (int)$filterData['attribute'],
                    $value,
                    $conjunction
                );
            }
        }

        $event = GeneralUtility::makeInstance(FilterConstraints::class, $demand, $query, $constraints);
        $this->dispatcher->dispatch(__CLASS__, 'filtersConstraintArray', [$event]);

        return $this->createConstraintFromConstraintsArray(
            $query,
            $event->getConstraints(),
            $demand->getFilterConjunction()
        );
    }

    /**
     * Create single filter attribute constraint.
     *
     * @param QueryInterface $query
     * @param int $attribute
     * @param array $values
     * @param string $conjunction
     * @return ConstraintInterface
     */
    protected function attributeFilterConstraint(
        QueryInterface $query,
        int $attribute,
        array $values,
        string $conjunction
    ): ConstraintInterface {
        // Create like constraint for each filter value
        $valueConstraints = array_map(function ($value) use ($query) {
            return $query->like('attributesValues.value', sprintf('%%,%s,%%', $value));
        }, $values);

        // Add attribute uid constraint to values constraints
        return $query->logicalAnd([
            $query->equals('attributesValues.attribute', $attribute),
            $this->createConstraintFromConstraintsArray($query, $valueConstraints, $conjunction),
        ]);
    }
}

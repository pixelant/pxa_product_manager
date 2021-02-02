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
use Pixelant\PxaProductManager\Domain\Model\Filter;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ProductRepository extends AbstractDemandRepository
{
    use AbleFindByUidList;

    /**
     * Returns object class name.
     *
     * @return string
     */
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

        $selectFields = [
            'tx_pxaproductmanager_domain_model_product.uid',
            'tx_pxaproductmanager_domain_model_product.name',
            'tx_pxaproductmanager_domain_model_product.teaser',
            'tx_pxaproductmanager_domain_model_product.sku',
            'tx_pxaproductmanager_domain_model_product.price',
            'tx_pxaproductmanager_domain_model_product.singleview_page',
            'tx_pxaproductmanager_domain_model_product.images',
            'tx_pxaproductmanager_domain_model_product.product_type',
        ];

        // fireEvent AfterSelectFieldsEvent table fields

        $queryBuilder
            ->select(...$selectFields)
            ->addSelect()
            ->from('tx_pxaproductmanager_domain_model_product');

        $this->fireDemandEvent('afterDemandQueryBuilderInitialize', $demand, $queryBuilder);

        $this->addStorageExpression($queryBuilder, $demand);

        $this->addProductPagesExpression($queryBuilder, $demand);

        $this->fireDemandEvent('beforeDemandQueryBuilderFilters', $demand, $queryBuilder);

        $this->addFilters($queryBuilder, $demand);

        $this->fireDemandEvent('afterDemandQueryBuilderFilters', $demand, $queryBuilder);

        $this->addLimit($queryBuilder, $demand);

        $this->addOffset($queryBuilder, $demand);

        $this->demandService->getSortBy([
            'tx_pxaproductmanager_domain_model_product' => 'tx_pxaproductmanager_domain_model_product',
        ], $queryBuilder);

        $this->fireDemandEvent('afterDemandQueryBuilder', $demand, $queryBuilder);

        return $queryBuilder;
    }

    /**
     * Add product pages to querybuilder expression if set.
     *
     * @param QueryBuilder $queryBuilder
     * @param DemandInterface $demand
     */
    protected function addProductPagesExpression(QueryBuilder $queryBuilder, DemandInterface $demand): void
    {
        $pageTreeStartingPoint = $demand->getPageTreeStartingPoint();
        if ($pageTreeStartingPoint) {
            $pids = $GLOBALS['TSFE']->cObj->getTreeList($pageTreeStartingPoint * -1, 5);
            $singleViewPages = GeneralUtility::intExplode(',', $pids, true);

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(
                    'uid',
                    $this->getProductPagesSubQuery($singleViewPages, $queryBuilder)
                )
            );
        }
    }

    /**
     * Returns product pages in subquery.
     *
     * @param array $singleViewPageIds
     * @param QueryBuilder $parentQueryBuilder
     * @return string
     */
    protected function getProductPagesSubQuery(array $singleViewPageIds, QueryBuilder $parentQueryBuilder): string
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

    /**
     * Get attribute filter subquery.
     *
     * @param int $attributeId
     * @param mixed $values
     * @param QueryBuilder $parentQueryBuilder
     * @param string $conjunction
     * @return string
     */
    protected function getAttributeSubQuery(
        int $attributeId,
        $values,
        QueryBuilder $parentQueryBuilder,
        string $conjunction
    ): string {
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

    /**
     * Get category filter subquery.
     *
     * @param int $categoryId
     * @param mixed $values
     * @param QueryBuilder $parentQueryBuilder
     * @param string $conjunction
     * @return string
     */
    protected function getCategoriesSubQuery(
        int $categoryId,
        $values,
        QueryBuilder $parentQueryBuilder,
        string $conjunction
    ): string {
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

    /**
     * Add filters to querybuilder expression if set.
     *
     * @param QueryBuilder $queryBuilder
     * @param DemandInterface $demand
     * @return void
     */
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
}

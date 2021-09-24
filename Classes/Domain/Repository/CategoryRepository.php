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

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CategoryRepository extends Repository
{
    use CanFindByUidList;

    /**
     * Default orderings.
     *
     * @var array
     */
    protected $defaultOrderings = [
        'sorting' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * Initializes the repository.
     */
    public function initializeObject(): void
    {
        /** @var Typo3QuerySettings $querySettings */
        $querySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(false);
        $this->setDefaultQuerySettings($querySettings);
    }

    /**
     * Find available categories uids by products sub-query.
     *
     * @param string $subQuery
     * @return array
     */
    public function findIdsByProductsSubQuery(string $subQuery): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
            'sys_category'
        );

        return $queryBuilder
            ->select('category.uid')
            ->from('sys_category', 'category')
            ->join(
                'category',
                'sys_category_record_mm',
                'mm',
                $queryBuilder->expr()->eq(
                    'category.uid',
                    $queryBuilder->quoteIdentifier('mm.uid_local')
                )
            )
            ->where(
                $queryBuilder->expr()->in('mm.uid_foreign', "(${subQuery})"),
                $queryBuilder->expr()->eq('mm.tablenames', $queryBuilder->createNamedParameter(
                    'tx_pxaproductmanager_domain_model_product',
                    \PDO::PARAM_STR
                )),
                $queryBuilder->expr()->eq('mm.fieldname', $queryBuilder->createNamedParameter(
                    'categories',
                    Connection::PARAM_STR
                ))
            )
            ->groupBy('category.uid')
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);
    }
}

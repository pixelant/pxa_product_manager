<?php

namespace Pixelant\PxaProductManager\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017
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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class AttributeValueRepository
 * @package Pixelant\PxaProductManager\Domain\Repository
 */
class AttributeValueRepository extends Repository
{
    /**
     * Find raw attribute value
     *
     * @param int $productUid
     * @param int $attributeUid
     * @return array|null
     */
    public function findRawByProductAndAttribute(int $productUid, int $attributeUid): ?array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_pxaproductmanager_domain_model_attributevalue');

        $row = $queryBuilder
            ->select('*')
            ->from('tx_pxaproductmanager_domain_model_attributevalue')
            ->where(
                $queryBuilder->expr()->eq('product', $queryBuilder->createNamedParameter($productUid, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('attribute', $queryBuilder->createNamedParameter($attributeUid, \PDO::PARAM_INT))
            )
            ->setMaxResults(1)
            ->execute()
            ->fetch();

        return is_array($row) ? $row : null;
    }

    /**
     * Update value field by uid
     *
     * @param int $uid
     * @param $value
     */
    public function updateValue(int $uid, $value): void
    {
        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_pxaproductmanager_domain_model_attributevalue')
            ->update(
                'tx_pxaproductmanager_domain_model_attributevalue',
                ['value' => $value],
                ['uid' => $uid]
            );
    }

    /**
     * Create with value for product and attribute
     *
     * @param int $product
     * @param int $attribute
     * @param $value
     * @return int Uid of created value
     */
    public function createWithValue(int $product, int $attribute, $value): int
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_pxaproductmanager_domain_model_attributevalue');

        $connection->insert(
            'tx_pxaproductmanager_domain_model_attributevalue',
            [
                'product' => $product,
                'attribute' => $attribute,
                'value' => $value,
            ]
        );

        return (int)$connection->lastInsertId('tx_pxaproductmanager_domain_model_attributevalue');
    }
}

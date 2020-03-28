<?php
declare(strict_types=1);

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

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use TYPO3\CMS\Core\Database\Connection;
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
     * Find all available values for product demand
     *
     * @param string $subQuery
     * @return array
     */
    public function findOptionIdsByProductSubQuery(string $subQuery): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
            'tx_pxaproductmanager_domain_model_attributevalue'
        );

        return $queryBuilder
            ->select('attributevalue.value')
            ->from('tx_pxaproductmanager_domain_model_attributevalue', 'attributevalue')
            ->join(
                'attributevalue',
                'tx_pxaproductmanager_domain_model_attribute',
                'attributes',
                $queryBuilder->expr()->eq(
                    'attributevalue.attribute',
                    $queryBuilder->quoteIdentifier('attributes.uid')
                )
            )
            ->where(
                $queryBuilder->expr()->in('attributevalue.product', "($subQuery)"),
                $queryBuilder->expr()->in(
                    'attributes.type',
                    $queryBuilder->createNamedParameter(
                        [Attribute::ATTRIBUTE_TYPE_DROPDOWN, Attribute::ATTRIBUTE_TYPE_MULTISELECT],
                        Connection::PARAM_INT_ARRAY
                    )
                )
            )
            ->groupBy('attributevalue.value')
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);
    }


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

        $expr = $queryBuilder->expr();
        $row = $queryBuilder
            ->select('*')
            ->from('tx_pxaproductmanager_domain_model_attributevalue')
            ->where(
                $expr->eq('product', $queryBuilder->createNamedParameter($productUid, \PDO::PARAM_INT)),
                $expr->eq('attribute', $queryBuilder->createNamedParameter($attributeUid, \PDO::PARAM_INT))
            )
            ->setMaxResults(1)
            ->execute()
            ->fetch();

        return is_array($row) ? $row : null;
    }


    /**
     * Find attribute value using product uid and attribute identifier
     *
     * @param int $productUid
     * @param string $identifier
     * @return array|null
     */
    public function findRawByProductAndAttributeIdentifier(int $productUid, string $identifier): ?array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_pxaproductmanager_domain_model_attributevalue');

        $expr = $queryBuilder->expr();
        $row = $queryBuilder
            ->select('attributevalue.*')
            ->from('tx_pxaproductmanager_domain_model_attributevalue', 'attributevalue')
            ->join(
                'attributevalue',
                'tx_pxaproductmanager_domain_model_attribute',
                'attribute',
                $expr->eq('attributevalue.attribute', $queryBuilder->quoteIdentifier('attribute.uid'))
            )
            ->where(
                $expr->eq('attributevalue.product', $queryBuilder->createNamedParameter($productUid, \PDO::PARAM_INT)),
                $expr->eq(
                    'attribute.identifier',
                    $queryBuilder->createNamedParameter($identifier, \PDO::PARAM_STR)
                )
            )
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

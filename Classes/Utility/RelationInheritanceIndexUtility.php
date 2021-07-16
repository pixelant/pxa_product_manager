<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Domain\Repository\RelationInheritanceIndexRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Convenience utility for relation inheritance index values.
 */
class RelationInheritanceIndexUtility
{
    /**
     * Update Relation Inheritance Index by Child Parent Id.
     * Makes sure relation inheritance index and attribute values are up to date for affected child products.
     *
     * @param int $childParentId
     * @return void
     */
    public static function updateRelationsByChildParentId(int $childParentId): void
    {
        $invalidRelations = self::findInvalidRelationsByChildParentId($childParentId);

        if (!empty($invalidRelations)) {
            foreach ($invalidRelations as $invalidRelation) {
                // Remove the child product invalid inherited attribute value.
                AttributeUtility::deleteAttributeValueRecord($invalidRelation['uid_child']);
                // Remove invalud relation inheritance index.
                self::removeAttributeValueRelationInheritanceIndexRecord(
                    $invalidRelation['uid_parent'],
                    $invalidRelation['uid_child'],
                    $invalidRelation['child_parent_id']
                );
            }
        }
    }

    /**
     * Finds all invalid relation inheritance index records by child_parent_id.
     *
     * @param int $childParentId
     * @return array
     */
    protected static function findInvalidRelationsByChildParentId(int $childParentId)
    {
        $queryBuilder = self::getQueryBuilderForTable(RelationInheritanceIndexRepository::TABLE_NAME);
        $queryBuilder->getRestrictions()->removeAll();

        $selectFields = [
            'tprii.uid_parent',
            'tprii.uid_child',
            'tprii.tablename',
            'tprii.child_parent_id',
            'tprii.child_parent_tablename',
        ];

        $result = $queryBuilder
            ->select(...$selectFields)
            ->from(RelationInheritanceIndexRepository::TABLE_NAME, 'tprii')
            ->join(
                'tprii',
                AttributeValueRepository::TABLE_NAME,
                'attrval_parent',
                $queryBuilder->expr()->eq(
                    'attrval_parent.uid',
                    $queryBuilder->quoteIdentifier('tprii.uid_parent')
                )
            )
            ->join(
                'tprii',
                ProductRepository::TABLE_NAME,
                'child_product',
                $queryBuilder->expr()->eq(
                    'child_product.uid',
                    $queryBuilder->quoteIdentifier('tprii.child_parent_id')
                )
            )
            ->where(
                $queryBuilder->expr()->eq(
                    'tprii.tablename',
                    $queryBuilder->createNamedParameter(
                        'tx_pxaproductmanager_domain_model_attributevalue',
                        \PDO::PARAM_STR
                    )
                ),
                $queryBuilder->expr()->eq(
                    'tprii.child_parent_tablename',
                    $queryBuilder->createNamedParameter(
                        'tx_pxaproductmanager_domain_model_product',
                        \PDO::PARAM_STR
                    )
                ),
                $queryBuilder->expr()->eq(
                    'tprii.child_parent_id',
                    $queryBuilder->createNamedParameter($childParentId, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->neq(
                    'child_product.parent',
                    'attrval_parent.product'
                ),
            )
            ->execute()
            ->fetchAllAssociative();

        if (is_array($result)) {
            return $result;
        }

        return [];
    }

    /**
     * Remove RelationInheritanceIndexRecord.
     *
     * @param int $uidParent
     * @param int $uidChild
     * @param int $childParentId
     * @return void
     */
    protected static function removeAttributeValueRelationInheritanceIndexRecord(
        int $uidParent,
        int $uidChild,
        int $childParentId
    ): void {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = self::getQueryBuilderForTable(RelationInheritanceIndexRepository::TABLE_NAME);

        $queryBuilder
            ->delete(RelationInheritanceIndexRepository::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid_parent', $queryBuilder->createNamedParameter($uidParent)),
                $queryBuilder->expr()->eq('uid_child', $queryBuilder->createNamedParameter($uidChild)),
                $queryBuilder->expr()->eq(
                    'tablename',
                    $queryBuilder->createNamedParameter(AttributeValueRepository::TABLE_NAME)
                ),
                $queryBuilder->expr()->eq('child_parent_id', $queryBuilder->createNamedParameter($childParentId)),
                $queryBuilder->expr()->eq(
                    'child_parent_tablename',
                    $queryBuilder->createNamedParameter(ProductRepository::TABLE_NAME)
                )
            )
            ->execute();
    }

    /**
     * @param string $table
     * @return QueryBuilder
     */
    protected static function getQueryBuilderForTable(string $table)
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
    }
}

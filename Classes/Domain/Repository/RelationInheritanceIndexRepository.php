<?php

namespace Pixelant\PxaProductManager\Domain\Repository;

/*
 *
 *  Copyright notice
 *
 *  (c) 2017
 *
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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for Filters.
 */
class RelationInheritanceIndexRepository extends Repository
{
    public const TABLE_NAME = 'tx_pxaproductmanager_relation_inheritance_index';

    /**
     * Returns the UID of a relations's "cousin" (attached to a parent product).
     *
     * This is useful for inline relations, where a child product must retain its own copies of the relations and where
     * there is no way to know which relation on the child product is the copy of which relation on the parent. To avoid
     * deleting and recreating all relations on the child object, calling this function and
     * addParentChildRelationToIndex() we can keep a record of the relationships.
     *
     *     +-----------------+
     *     | Child product   |
     *     |                 |      +-----------------+
     *     | $inlineRelation | ---- | Relation record | -> index table
     *     +-----------------+      +-----------------+        |
     *             |                                           |
     *             |                                           |
     *     +-----------------+                                 |
     *     | Parent product  |                                 |
     *     |                 |      +-----------------+        V
     *     | $inlineRelation | ---- | Relation record | <- "cousin"
     *     +-----------------+      +-----------------+
     *
     * @param int $childUid
     * @param string $tablename
     * @param int $childParentId
     * @param string $childParentTable
     * @return int Uid of the record. Zero if not found
     */
    public function findParentRelationUidInIndex(
        int $childUid,
        string $tablename,
        int $childParentId,
        string $childParentTable
    ): int {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE_NAME);

        return (int)$queryBuilder
            ->select('uid_parent')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid_child', $queryBuilder->createNamedParameter($childUid)),
                $queryBuilder->expr()->eq('tablename', $queryBuilder->createNamedParameter($tablename)),
                $queryBuilder->expr()->eq('child_parent_id', $queryBuilder->createNamedParameter($childParentId)),
                $queryBuilder->expr()->eq(
                    'child_parent_tablename',
                    $queryBuilder->createNamedParameter($childParentTable)
                )
            )
            ->execute()
            ->fetchOne();
    }

    /**
     * Returns the uid of the record on the child relation side.
     *
     * @see ProductInheritanceProcessDatamap::findParentRelationUidInIndex()
     *
     * @param int $parentUid
     * @param string $tablename
     * @param int $childParentId
     * @param string $childParentTable
     * @return int
     */
    public function findChildRelationUidInIndex(
        int $parentUid,
        string $tablename,
        int $childParentId,
        string $childParentTable
    ): int {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE_NAME);

        return (int)$queryBuilder
            ->select('uid_child')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid_parent', $queryBuilder->createNamedParameter($parentUid)),
                $queryBuilder->expr()->eq('tablename', $queryBuilder->createNamedParameter($tablename)),
                $queryBuilder->expr()->eq('child_parent_id', $queryBuilder->createNamedParameter($childParentId)),
                $queryBuilder->expr()->eq(
                    'child_parent_tablename',
                    $queryBuilder->createNamedParameter($childParentTable)
                )
            )
            ->execute()
            ->fetchOne();
    }

    /**
     * Index a relation copy.
     *
     * @see ProductInheritanceProcessDatamap::findParentRelationUidInIndex()
     *
     * @param int $parentUid
     * @param int $childUid
     * @param string $tablename
     * @param int $childParentId
     * @param string $childParentTable
     */
    public function addParentChildRelationToIndex(
        int $parentUid,
        int $childUid,
        string $tablename,
        int $childParentId,
        string $childParentTable
    ): void {
        if (
            $parentUid === 0
            || $childUid === 0
            || $this->findParentRelationUidInIndex(
                $childUid,
                $tablename,
                $childParentId,
                $childParentTable
            ) === $parentUid
        ) {
            return;
        }

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE_NAME);

        $queryBuilder
            ->insert(self::TABLE_NAME)
            ->values([
                'uid_parent' => $parentUid,
                'uid_child' => $childUid,
                'tablename' => $tablename,
                'child_parent_id' => $childParentId,
                'child_parent_tablename' => $childParentTable,
            ])
            ->execute();
    }

    /**
     * Remove a relation copy parent from the index.
     *
     * @see ProductInheritanceProcessDatamap::findParentRelationUidInIndex()
     *
     * @param int $parentUid
     * @param string $tablename
     * @param int $childParentId
     * @param string $childParentTable
     */
    public function removeParentRelationsFromIndex(
        int $parentUid,
        string $tablename,
        int $childParentId,
        string $childParentTable
    ): void {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::TABLE_NAME);

        $queryBuilder
            ->delete(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid_parent', $queryBuilder->createNamedParameter($parentUid)),
                $queryBuilder->expr()->eq('tablename', $queryBuilder->createNamedParameter($tablename)),
                $queryBuilder->expr()->eq('child_parent_id', $queryBuilder->createNamedParameter($childParentId)),
                $queryBuilder->expr()->eq(
                    'child_parent_tablename',
                    $queryBuilder->createNamedParameter($childParentTable)
                )
            )
            ->execute();
    }
}

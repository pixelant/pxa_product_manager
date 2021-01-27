<?php

declare(strict_types=1);


namespace Pixelant\PxaProductManager\Utility;


use Pixelant\PxaProductManager\Domain\Repository\AttributeRepository;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Convenience utility for attributes and attribute values
 */
class AttributeUtility
{
    /**
     * Get an attrib
     *
     * @param int $attributeId UID of the attribute
     * @param string $selectFields List of fields to select (comma-separated)
     * @return array|null
     */
    public static function getAttribute(int $attributeId, string $selectFields = '*'): ?array
    {
        return BackendUtility::getRecord(
            AttributeRepository::TABLE_NAME,
            $attributeId,
            $selectFields
        );
    }

    /**
     * Get all attribute records.
     *
     * @param string $selectFields List of fields to select (comma-separated)
     * @param string $where Additional WHERE clause, eg. ' AND some_field = 0'
     * @return array
     */
    public static function findAllAttributes(string $selectFields = '*', string $where = ''): array
    {
        $queryBuilder = self::getQueryBuilderForTable(AttributeRepository::TABLE_NAME);

        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        // add custom where clause
        if ($where !== '') {
            $queryBuilder->andWhere(QueryHelper::stripLogicalOperatorPrefix($where));
        }

        $row = $queryBuilder
            ->select(...GeneralUtility::trimExplode(',', $selectFields, true))
            ->from(AttributeRepository::TABLE_NAME)
            ->execute()
            ->fetchAllAssociative();

        if (is_array($row)) {
            return $row;
        }

        return [];
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

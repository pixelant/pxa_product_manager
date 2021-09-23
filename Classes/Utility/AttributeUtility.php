<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

use Pixelant\PxaProductManager\Attributes\ValueMapper\FalMapper;
use Pixelant\PxaProductManager\Attributes\ValueMapper\SelectBoxMapper;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Repository\AttributeRepository;
use Pixelant\PxaProductManager\Domain\Repository\AttributeSetRepository;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Domain\Repository\OptionRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductTypeRepository;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Convenience utility for attributes and attribute values.
 */
class AttributeUtility
{
    /**
     * Get an attrib.
     *
     * @param int $attributeId UID of the attribute
     * @param string $selectFields List of fields to select (comma-separated)
     * @return array|null
     */
    public static function findAttribute(int $attributeId, string $selectFields = '*'): ?array
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
     * Returns all attribute records for the specified $productTypeId.
     *
     * @param int $productTypeId
     * @return array
     */
    public static function findAttributesForProductType(int $productTypeId): array
    {
        if ($productTypeId === 0) {
            return [];
        }

        $attributeSets = self::findAttributeSetsForProductType($productTypeId);

        $attributes = [];
        foreach ($attributeSets as $attributeSet) {
            $attributes = array_merge($attributes, self::findAttributesForAttributeSet((int)$attributeSet['uid']));
        }

        return $attributes;
    }

    /**
     * Find attribute sets for the given product type.
     *
     * @param int $productTypeId
     * @return array of product set records
     */
    public static function findAttributeSetsForProductType(int $productTypeId): array
    {
        if ($productTypeId === 0) {
            return [];
        }

        $fieldTcaConfiguration = BackendUtility::getTcaFieldConfiguration(
            ProductTypeRepository::TABLE_NAME,
            'attribute_sets'
        );

        /** @var RelationHandler $relationHandler */
        $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);
        $relationHandler->start(
            '',
            AttributeSetRepository::TABLE_NAME,
            $fieldTcaConfiguration['MM'],
            $productTypeId,
            ProductTypeRepository::TABLE_NAME,
            $fieldTcaConfiguration
        );

        return $relationHandler->getFromDB()[AttributeSetRepository::TABLE_NAME] ?? [];
    }

    /**
     * Find attributes for an attribute set.
     *
     * @param int $attributeSetId
     * @return array of attribute records
     */
    public static function findAttributesForAttributeSet(int $attributeSetId): array
    {
        if ($attributeSetId === 0) {
            return [];
        }

        $fieldTcaConfiguration = BackendUtility::getTcaFieldConfiguration(
            AttributeSetRepository::TABLE_NAME,
            'attributes'
        );

        /** @var RelationHandler $relationHandler */
        $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);
        $relationHandler->start(
            '',
            AttributeRepository::TABLE_NAME,
            $fieldTcaConfiguration['MM'],
            $attributeSetId,
            AttributeSetRepository::TABLE_NAME,
            $fieldTcaConfiguration
        );

        return $relationHandler->getFromDB()[AttributeRepository::TABLE_NAME] ?? [];
    }

    /**
     * Find a specific attribute value for a product.
     *
     * @param int $productId
     * @param array $attributeIds
     * @param string $selectFields
     * @return array|null
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public static function findAttributeValues(int $productId, array $attributeIds, string $selectFields = '*'): ?array
    {
        $queryBuilder = self::getQueryBuilderForTable(AttributeValueRepository::TABLE_NAME);

        $row = $queryBuilder
            ->select(...GeneralUtility::trimExplode(',', $selectFields, true))
            ->from(AttributeValueRepository::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('product', $queryBuilder->createNamedParameter($productId)),
                $queryBuilder->expr()->in('attribute', $queryBuilder->createNamedParameter($attributeIds))
            )
            ->execute()
            ->fetchAssociative();

        if (is_array($row)) {
            return $row;
        }

        return null;
    }

    /**
     * @param int $productId
     * @param int $attributeId
     * @param string $selectFields
     * @return array|null
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public static function findAttributeValue(int $productId, int $attributeId, string $selectFields = '*'): ?array
    {
        return self::findAttributeValues($productId, [$attributeId], $selectFields);
    }

    /**
     * Getting corresponding render value of attribute value. Without Extbase entities, only simple types.
     *
     * @param int $uid
     * @return mixed
     */
    public static function getAttributeValueRenderValue(int $uid)
    {
        $queryBuilder = self::getQueryBuilderForTable(AttributeValueRepository::TABLE_NAME);

        $attributeValue = $queryBuilder
            ->select('*')
            ->from(AttributeValueRepository::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
            )
            ->execute()
            ->fetchAssociative();

        $attribute = self::findAttribute($attributeValue['attribute']);

        if (
            $attribute['type'] === Attribute::ATTRIBUTE_TYPE_IMAGE ||
            $attribute['type'] === Attribute::ATTRIBUTE_TYPE_FILE
        ) {
            return GeneralUtility::makeInstance(FalMapper::class)->mapToArray($attributeValue['uid']);
        }
        if (
            $attribute['type'] === Attribute::ATTRIBUTE_TYPE_MULTISELECT ||
            $attribute['type'] === Attribute::ATTRIBUTE_TYPE_DROPDOWN
        ) {
            return GeneralUtility::makeInstance(SelectBoxMapper::class)
                ->mapToArray($attributeValue['value'], $attributeValue['attribute']);
        }

        return $attributeValue['value'];
    }

    /**
     * Find options for attribute.
     *
     * @param int $attributeId
     * @param string $selectFields
     * @return array|null
     */
    public static function findAttributeOptions(int $attributeId, string $selectFields = '*'): ?array
    {
        $queryBuilder = self::getQueryBuilderForTable(OptionRepository::TABLE_NAME);

        $row = $queryBuilder
            ->select(...GeneralUtility::trimExplode(',', $selectFields, true))
            ->from(OptionRepository::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('attribute', $queryBuilder->createNamedParameter($attributeId))
            )
            ->execute()
            ->fetchAllAssociative();

        if (is_array($row)) {
            return $row;
        }

        return null;
    }

    /**
     * Remove attribute value record.
     *
     * @param int $uid
     * @return void
     */
    public static function deleteAttributeValueRecord(int $uid): void
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(AttributeValueRepository::TABLE_NAME);

        $queryBuilder
            ->delete(AttributeValueRepository::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
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

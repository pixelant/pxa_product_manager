<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Repository\AttributeRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Event\DataInheritance\CalculateInlineFieldValueEvent;
use Pixelant\PxaProductManager\Event\DataInheritance\InheritNewInlineDataEvent;
use Pixelant\PxaProductManager\Event\DataInheritance\InlineIdentifierFieldEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataGroup\TcaDatabaseRecord;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Convenience methods for handling inheritance of product properties and parent/child relations.
 */
class DataInheritanceUtility
{
    protected static array $productTypeInheritedFields = [];

    protected static array $productTypeInheritedDbFields = [];

    protected static array $productTypeForProduct = [];

    /**
     * Get an array of field names that are specified as inherited from parent products for this product type.
     *
     * @param int $productType ProductType UID
     * @return array
     */
    public static function getInheritedFieldsForProductType(int $productType): array
    {
        if ($productType === 0) {
            return [];
        }

        if (isset(self::$productTypeInheritedFields[$productType])) {
            return self::$productTypeInheritedFields[$productType];
        }

        $productTypeRecord = BackendUtility::getRecord(
            'tx_pxaproductmanager_domain_model_producttype',
            $productType,
            'inherit_fields'
        );

        self::$productTypeInheritedFields[$productType]
            = GeneralUtility::trimExplode(',', $productTypeRecord['inherit_fields'] . ',product_type', true);

        return self::$productTypeInheritedFields[$productType];
    }

    /** Get product type for product.
     *
     * @param int $productId
     * @return int
     */
    public static function getProductTypeForProduct(int $productId): int
    {
        if ($productId === 0) {
            return 0;
        }

        if (isset(self::$productTypeForProduct[$productId])) {
            return self::$productTypeForProduct[$productId];
        }

        self::$productTypeForProduct[$productId] = (int)BackendUtility::getRecord(
            ProductRepository::TABLE_NAME,
            $productId,
            'product_type'
        )['product_type'] ?? 0;

        return self::$productTypeForProduct[$productId];
    }

    /**
     * Get inherited db fields.
     *
     * @param int $productType
     * @return array
     */
    public static function getInheritedDbFieldsForProductType(int $productType): array
    {
        if ($productType === 0) {
            return [];
        }

        if (isset(self::$productTypeInheritedDbFields[$productType])) {
            return self::$productTypeInheritedDbFields[$productType];
        }

        $includeAttributeValues = false;
        $inheritedFields = self::getInheritedFieldsForProductType($productType);
        $inheritedDbFields = [];

        foreach ($inheritedFields as $inheritedField) {
            if (strpos($inheritedField, 'attribute.') !== false) {
                $includeAttributeValues = true;
            } else {
                $inheritedDbFields[] = $inheritedField;
            }
        }

        if ($includeAttributeValues) {
            $inheritedDbFields[] = 'attributes_values';
        }

        self::$productTypeInheritedDbFields[$productType] = $inheritedDbFields;

        return self::$productTypeInheritedDbFields[$productType];
    }

    /**
     * Get resolvedItemArray from RelationHandler.
     *
     * @param array $fieldTcaConfiguration
     * @param int $mmUid
     * @param string $currentTable
     * @return array
     */
    public static function getRelationHandlerResolvedItems(
        array $fieldTcaConfiguration,
        int $mmUid,
        string $currentTable = ProductRepository::TABLE_NAME
    ): array {
        $foreignTable = $fieldTcaConfiguration['foreign_table'];

        /** @var RelationHandler $relationHandler */
        $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);
        $relationHandler->start(
            '',
            $foreignTable,
            '',
            $mmUid,
            $currentTable,
            $fieldTcaConfiguration
        );
        $relationHandler->getFromDB();

        return $relationHandler->getResolvedItemArray();
    }

    /**
     * Calculates a hash for the inheritance fields value.
     *
     * @param int $productId The uid of the product.
     * @return string
     */
    public static function calculateInheritanceHash(int $productId): string
    {
        return md5(json_encode(self::buildInheritedFieldValuesArray($productId))) ?? '';
    }

    /**
     * Build array of field values for inherited fields.
     * Try and parse the actual value for relations.
     * E.g. need to fetch the id of the file a sys_file_refenence points at,
     * and not use the id of the sys_file_reference.
     *
     * @param inte $productId
     * @return array
     */
    public static function buildInheritedFieldValuesArray(int $productId): array
    {
        $inheritedFieldValues = [];

        $productType = self::getProductTypeForProduct($productId);
        if ($productType === 0) {
            return [];
        }

        $inheritedFields = self::getInheritedFieldsForProductType(self::getProductTypeForProduct($productType));
        if (count($inheritedFields) === 0) {
            return [];
        }

        $productInheritedFieldsRecord = self::getCompiledProductRecord($productId, true);

        foreach ($productInheritedFieldsRecord as $key => $value) {
            $fieldTcaConfiguration = TcaUtility::getTcaFieldConfigurationAndRespectColumnsOverrides(
                ProductRepository::TABLE_NAME,
                $key,
                $productInheritedFieldsRecord
            );

            if ($fieldTcaConfiguration['type'] === 'inline') {
                $resolvedItemsList = self::resolveInlineInheritedFields(
                    $fieldTcaConfiguration,
                    $productId
                );

                if ($key === 'attributes_values') {
                    foreach ($resolvedItemsList as $attributeValue) {
                        [$uid, $value] = GeneralUtility::trimExplode('|', $attributeValue);
                        $attributeKey = 'attribute.' . $uid;
                        if (in_array($attributeKey, $inheritedFields, true)) {
                            $inheritedFieldValues[$attributeKey] = $value;
                        }
                    }
                } else {
                    $inheritedFieldValues[$key] = implode(',', $resolvedItemsList);
                }
            } else {
                $inheritedFieldValues[$key] = $value;
            }
        }

        return $inheritedFieldValues;
    }

    /**
     * Resolve inline inherited fields.
     *
     * @param array $fieldTcaConfiguration
     * @param int $productId
     * @return array
     */
    protected static function resolveInlineInheritedFields(array $fieldTcaConfiguration, int $productId): array
    {
        $eventDispatcher = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(EventDispatcherInterface::class);

        $resolvedItemsList = [];

        $resolvedItems = self::getRelationHandlerResolvedItems(
            $fieldTcaConfiguration,
            $productId
        );

        if (!empty($resolvedItems)) {
            foreach ($resolvedItems as $item) {
                $resolvedItemsList[]
                    = $eventDispatcher->dispatch(
                        new CalculateInlineFieldValueEvent(
                            $item['table'],
                            $item['uid']
                        )
                    )->getValue();
            }
        }

        return $resolvedItemsList;
    }

    /**
     * Compile a product, i.e. return the product with the data that would be saved with it in a form.
     *
     * @param int $productId
     * @param bool $onlyIncludeInheritedFields Only include inherited fields based on the products product type.
     * @return array
     */
    protected static function getCompiledProductRecord(int $productId, bool $onlyIncludeInheritedFields = false): array
    {
        $compiledProductRecord = self::compileRecordData(ProductRepository::TABLE_NAME, $productId);

        if ($onlyIncludeInheritedFields) {
            $inheritedDbFields = self::getInheritedDbFieldsForProductType(
                self::getProductTypeForProduct((int)$compiledProductRecord['product_type'])
            );

            $compiledProductRecord = array_filter(
                $compiledProductRecord,
                fn ($key) => in_array($key, $inheritedDbFields, true),
                ARRAY_FILTER_USE_KEY
            );
        }

        return $compiledProductRecord;
    }

    /**
     * Generate list of uids for object storage data.
     *
     * @param ObjectStorage $objectStorage
     * @return string
     */
    public static function getObjectStorageIdList(ObjectStorage $objectStorage): string
    {
        $values = [];
        if (count($objectStorage) > 0) {
            foreach ($objectStorage as $item) {
                /** @var FileReference $item */
                if (method_exists($item, 'getOriginalResource')) {
                    $values[] = $item->getOriginalResource()->getOriginalFile()->getUid();
                } else {
                    if ($item instanceof \Pixelant\PxaProductManager\Domain\Model\Link) {
                        $values[] = $item->getLink();
                    } else {
                        $values[] = $item->getUid();
                    }
                }
            }
        }

        return implode(',', $values);
    }

    /**
     * Computes Attribute Values inherited fields data.
     *
     * @param array $parentInheritanceData
     * @param array $inheritedAttributes
     * @param int $childProductId
     * @param int $parentProductId
     * @return array
     */
    public static function computeAttributeValuesInheritanceData(
        array $parentInheritanceData,
        array $childInheritanceData,
        array $inheritedAttributes,
        int $childProductId,
        int $parentProductId
    ): array {
        $computedInlineInheritanceData = [];

        $fieldTcaConfiguration = TcaUtility::getTcaFieldConfigurationAndRespectColumnsOverrides(
            ProductRepository::TABLE_NAME,
            'attributes_values',
            $childInheritanceData
        );

        $resolvedChildItemsList = self::getRelationHandlerResolvedItems(
            $fieldTcaConfiguration,
            $childProductId
        );

        $resolvedParentItemsList = self::getRelationHandlerResolvedItems(
            $fieldTcaConfiguration,
            $parentProductId
        );

        $foreignTable = $fieldTcaConfiguration['foreign_table'];

        self::addIndentifiersToResolvedRelationItems($resolvedParentItemsList, $foreignTable);

        self::addIndentifiersToResolvedRelationItems($resolvedChildItemsList, $foreignTable);

        // Any inherited attributes missing?
        $missingChildAttributeValues = self::listMissingChildAttributeValues(
            $resolvedChildItemsList,
            $inheritedAttributes
        );

        // create missing attributes, add to child resolved items array data.
        if (!empty($missingChildAttributeValues)) {
            foreach ($missingChildAttributeValues as $missingChildAttributeValue) {
                $parentItemIndex = self::resolvedItemIndexByIdentifier(
                    $resolvedParentItemsList,
                    (string)$missingChildAttributeValue
                );
                $parentItem = $resolvedParentItemsList[$parentItemIndex];
                $table = $parentItem['table'];
                $id = $parentItem['uid'];

                $compiledParentRecord = self::compileRecordData(
                    $table,
                    (int)$id,
                    false
                );

                $resolvedChildItemsList[count($resolvedChildItemsList)] = [
                    'table' => $parentItem['table'],
                    'uid' => StringUtility::getUniqueId('NEW'),
                    'identifier' => $parentItem['identifier'],
                    'parent_sorting' => $parentItemIndex,
                    'data' => [
                        'value' => $compiledParentRecord['value'],
                        'attribute' => $compiledParentRecord['attribute'],
                        'pid' => $compiledParentRecord['pid'],
                    ],
                ];
            }
        }

        self::addParentSortingToResolvedChildRelationItems($resolvedChildItemsList, $resolvedParentItemsList);

        $attributeTypes = self::listAttributeTypesOfInheritedAttributeValues($inheritedAttributes);

        foreach ($inheritedAttributes as $attribute) {
            $inheritedAttributeId = (string)array_pop(explode('.', (string)$attribute ?? ''));
            $childItemIndex = self::resolvedItemIndexByIdentifier($resolvedChildItemsList, $inheritedAttributeId);

            switch ($attributeTypes[$inheritedAttributeId]) {
                case Attribute::ATTRIBUTE_TYPE_FILE:
                case Attribute::ATTRIBUTE_TYPE_IMAGE:
                    // Needs to be fixed to inherit with inline relation.
                    $resolvedChildItemsList[$childItemIndex]['data']['value'] = $parentInheritanceData[$attribute];

                    break;
                default:
                    $resolvedChildItemsList[$childItemIndex]['data']['value'] = $parentInheritanceData[$attribute];

                    break;
            }
        }

        // Fix sorting like "parent".
        usort($resolvedChildItemsList, function ($a, $b) {
            return $a['parent_sorting'] > $b['parent_sorting'];
        });

        // Add field to array.
        $computedInlineInheritanceData['data'][ProductRepository::TABLE_NAME][$childProductId]['attributes_values']
            = implode(',', array_column($resolvedChildItemsList, 'uid'));

        // Add missing relation records to array.
        foreach ($resolvedChildItemsList as $cResolvedItem) {
            if (isset($cResolvedItem['data'])) {
                $computedInlineInheritanceData['data'][$cResolvedItem['table']][$cResolvedItem['uid']]
                    = $cResolvedItem['data'];
            }
        }

        return $computedInlineInheritanceData;
    }

    /**
     * Returns array index for item with identifier.
     *
     * @param array $resolvedItems
     * @param string $identifier
     * @return int
     * @throws \UnexpectedValueException
     */
    protected static function resolvedItemIndexByIdentifier(array $resolvedItems, string $identifier): int
    {
        foreach ($resolvedItems as $index => $resolvedItem) {
            if ((string)$resolvedItem['identifier'] === $identifier) {
                return $index;
            }
        }

        throw new \UnexpectedValueException('No item with identifier found in array', 1631180810);
    }

    /**
     * List inherited attribute values child is missing.
     *
     * @param array $resolvedChildItemsList
     * @param array $inheritedAttributes
     * @return array
     */
    protected static function listMissingChildAttributeValues(
        array $resolvedChildItemsList,
        array $inheritedAttributes
    ): array {
        $missingAttributes = [];
        $childAttributeIdList = array_column($resolvedChildItemsList, 'identifier');

        foreach ($inheritedAttributes as $attribute) {
            $inheritedAttributeId = (int)array_pop(explode('.', (string)$attribute ?? ''));
            if (!in_array($inheritedAttributeId, $childAttributeIdList, true)) {
                $missingAttributes[] = $inheritedAttributeId;
            }
        }

        return $missingAttributes;
    }

    /**
     * Creates a list of attribute types from inherited attributes.
     * inheritedAttributes is an array of 'attribute.x' were x is the acutal attribute_value uid.
     *
     * @param array $inheritedAttributes
     * @return array
     */
    protected static function listAttributeTypesOfInheritedAttributeValues(
        array $inheritedAttributes
    ): array {
        $attributes = [];
        foreach ($inheritedAttributes as $attribute) {
            $attributeId = (int)array_pop(explode('.', (string)$attribute ?? ''));
            $type = (int)BackendUtility::getRecord(
                AttributeRepository::TABLE_NAME,
                $attributeId,
                'type'
            )['type'] ?? 0;
            $attributes[$attributeId] = $type;
        }

        return $attributes;
    }

    /**
     * Compute inline inheritance data array.
     * Uses parent relations to order inherited relations.
     *
     * @param string $field
     * @param array $fieldTcaConfiguration
     * @param int $childProductId
     * @param int $parentProductId
     * @return array
     */
    public static function computeInlineInheritanceData(
        string $field,
        array $fieldTcaConfiguration,
        int $childProductId,
        int $parentProductId
    ): array {
        $computedInlineInheritanceData = [];

        $resolvedChildItemsList = self::getRelationHandlerResolvedItems(
            $fieldTcaConfiguration,
            $childProductId
        );

        $resolvedParentItemsList = self::getRelationHandlerResolvedItems(
            $fieldTcaConfiguration,
            $parentProductId
        );

        $foreignTable = $fieldTcaConfiguration['foreign_table'];

        self::addIndentifiersToResolvedRelationItems($resolvedParentItemsList, $foreignTable);

        self::addIndentifiersToResolvedRelationItems($resolvedChildItemsList, $foreignTable);

        self::addParentSortingToResolvedChildRelationItems($resolvedChildItemsList, $resolvedParentItemsList);

        self::addMissingChildRelationItems($resolvedChildItemsList, $resolvedParentItemsList);

        $removedRelations = [];

        self::removeChildRelationsNotInParent($resolvedChildItemsList, $removedRelations);

        if (!empty($removedRelations)) {
            $computedInlineInheritanceData['cmd'] = $removedRelations;
        }

        // Fix sorting like "parent".
        usort($resolvedChildItemsList, function ($a, $b) {
            return $a['parent_sorting'] > $b['parent_sorting'];
        });

        // Add field to array.
        $computedInlineInheritanceData['data'][ProductRepository::TABLE_NAME][$childProductId][$field]
            = implode(',', array_column($resolvedChildItemsList, 'uid'));

        // Add missing relation records to array.
        foreach ($resolvedChildItemsList as $cResolvedItem) {
            if (!MathUtility::canBeInterpretedAsInteger($cResolvedItem['uid'])) {
                $computedInlineInheritanceData['data'][$cResolvedItem['table']][$cResolvedItem['uid']]
                    = $cResolvedItem['data'];
            }
        }

        return $computedInlineInheritanceData;
    }

    /**
     * Adds "real" identifiers to resolved relations.
     *
     * @param array &$resolvedRelationsItems
     * @return void
     */
    protected static function addIndentifiersToResolvedRelationItems(
        array &$resolvedRelationsItems,
        string $foreignTable
    ): void {
        $eventDispatcher = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(EventDispatcherInterface::class);

        $identifierField = $eventDispatcher
            ->dispatch(new InlineIdentifierFieldEvent($foreignTable))
            ->getField();

        foreach ($resolvedRelationsItems as $index => $resolvedItem) {
            $identifier = BackendUtility::getRecord(
                $resolvedItem['table'],
                $resolvedItem['uid'],
                $identifierField
            )[$identifierField];

            if (!empty($identifier)) {
                $resolvedRelationsItems[$index]['identifier'] = $identifier;
            }
        }
    }

    /**
     * Compares resolved child relations and adds parent sorting to child items.
     *
     * @param array $resolvedChildItemsList
     * @param array $resolvedParentItemsList
     * @return void
     */
    protected static function addParentSortingToResolvedChildRelationItems(
        array &$resolvedChildItemsList,
        array $resolvedParentItemsList
    ): void {
        foreach ($resolvedParentItemsList as $pIndex => $pResolvedItem) {
            foreach ($resolvedChildItemsList as $cIndex => $cResolvedItem) {
                if ($cResolvedItem['identifier'] === $pResolvedItem['identifier']) {
                    $resolvedChildItemsList[$cIndex]['parent_sorting'] = $pIndex;
                }
            }
        }
    }

    /**
     * Check if child relation exist by identifier.
     *
     * @param string $identifier
     * @param array $resolvedChildItemsList
     * @return bool
     */
    protected static function childRelationHaveItemWithIdentifier(
        string $identifier,
        array $resolvedChildItemsList
    ): bool {
        foreach ($resolvedChildItemsList as $resolvedChildItem) {
            if ((string)$resolvedChildItem['identifier'] === $identifier) {
                return true;
            }
        }

        return false;
    }

    /**
     * Adds "DataHandler" data to child relations missing parent data.
     *
     * @param array $resolvedChildItemsList
     * @param array $resolvedParentItemsList
     * @return void
     */
    protected static function addMissingChildRelationItems(
        array &$resolvedChildItemsList,
        array $resolvedParentItemsList
    ): void {
        $eventDispatcher = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(EventDispatcherInterface::class);

        foreach ($resolvedParentItemsList as $pIndex => $pResolvedItem) {
            $childHasRelation = self::childRelationHaveItemWithIdentifier(
                (string)$pResolvedItem['identifier'],
                $resolvedChildItemsList
            );
            if (!$childHasRelation) {
                $data = $eventDispatcher
                    ->dispatch(
                        new InheritNewInlineDataEvent(
                            $pResolvedItem['table'],
                            $pResolvedItem['uid'],
                            (int)$pResolvedItem['identifier']
                        )
                    )
                    ->getData();

                $resolvedChildItemsList[count($resolvedChildItemsList)] = [
                    'table' => $pResolvedItem['table'],
                    'uid' => StringUtility::getUniqueId('NEW'),
                    'identifier' => $pResolvedItem['identifier'],
                    'parent_sorting' => $pIndex,
                    'data' => $data,
                ];
            }
        }
    }

    /**
     * Remove child inline relations not present in parent.
     *
     * @param array $resolvedChildItemsList
     * @param array $resolvedChildItemsList
     * @return void
     */
    protected static function removeChildRelationsNotInParent(
        array &$resolvedChildItemsList,
        array &$removedRelations
    ): void {
        foreach ($resolvedChildItemsList as $cIndex => $cResolvedItem) {
            if (!isset($cResolvedItem['parent_sorting'])) {
                $removedRelations[$cResolvedItem['table']][$cResolvedItem['uid']]['delete'] = 1;
                unset($resolvedChildItemsList[$cIndex]);
            }
        }
    }

    /**
     * Test to inherit data using models.
     *
     * @param int $productId The uid of the product.
     * @return array
     */
    public static function inheritDataFromParent(int $productId): array
    {
        $inheritanceData = [];

        $parentProductId = (int)BackendUtility::getRecord(
            ProductRepository::TABLE_NAME,
            $productId,
            'parent'
        )['parent'] ?? 0;

        if ($parentProductId === 0) {
            return [];
        }

        $parentInheritanceData = self::buildInheritedFieldValuesArray($parentProductId);
        $childInheritanceData = self::buildInheritedFieldValuesArray($productId);
        $inheritedAttributes = [];

        foreach (array_keys($parentInheritanceData) as $inheritField) {
            if ($parentInheritanceData[$inheritField] !== $childInheritanceData[$inheritField]) {
                $fieldTcaConfiguration = TcaUtility::getTcaFieldConfigurationAndRespectColumnsOverrides(
                    ProductRepository::TABLE_NAME,
                    $inheritField,
                    $childInheritanceData
                );

                if ($fieldTcaConfiguration['type'] === 'inline') {
                    $computedInheritanceData = self::computeInlineInheritanceData(
                        $inheritField,
                        $fieldTcaConfiguration,
                        $productId,
                        $parentProductId
                    );

                    ArrayUtility::mergeRecursiveWithOverrule($inheritanceData, $computedInheritanceData);
                } else {
                    if (strpos($inheritField, 'attribute.') !== false) {
                        // Collect all inherited attributes first, process separate later.
                        $inheritedAttributes[] = $inheritField;
                    } else {
                        $inheritanceData['data'][ProductRepository::TABLE_NAME][$productId][$inheritField]
                            = $parentInheritanceData[$inheritField];
                    }
                }
            }
        }

        if (!empty($inheritedAttributes)) {
            $computedAttributeInheritanceData = self::computeAttributeValuesInheritanceData(
                $parentInheritanceData,
                $childInheritanceData,
                $inheritedAttributes,
                $productId,
                $parentProductId
            );
            ArrayUtility::mergeRecursiveWithOverrule($inheritanceData, $computedAttributeInheritanceData);
        }

        if (!empty($inheritanceData)) {
            // Flag data as "inherited" to avoid recursion in ProcessDatamap hook.
            $inheritanceData['data'][ProductRepository::TABLE_NAME][$productId]['is_inherited'] = 1;
        }

        return $inheritanceData;
    }

    /**
     * Compile a record, i.e. return the record with the data that would be saved with it in a form.
     *
     * @param string $tableName
     * @param int $vanillaUid
     * @param bool $removeUnprocessedColumns Remove columns that are not processed
     * @return mixed
     */
    public static function compileRecordData(string $tableName, int $vanillaUid, bool $removeUnprocessedColumns = false)
    {
        $formDataGroup = GeneralUtility::makeInstance(TcaDatabaseRecord::class);
        $formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class, $formDataGroup);

        $formDataCompilerInput = [
            'tableName' => $tableName,
            'vanillaUid' => $vanillaUid,
            'command' => 'edit',
        ];

        $result = $formDataCompiler->compile($formDataCompilerInput);
        $row = $result['databaseRow'];

        if ($removeUnprocessedColumns) {
            $row = array_filter(
                $row,
                fn ($key) => in_array($key, $result['columnsToProcess'], true),
                ARRAY_FILTER_USE_KEY
            );
        }

        foreach ($row as $key => $value) {
            if (is_array($value)) {
                $relations = [];

                foreach ($value as $item) {
                    if (is_array($item)) {
                        $relations[] = $item['table'] . '_' . $item['uid'];
                    } else {
                        $relations[] = $item;
                    }
                }

                $row[$key] = implode(',', $relations);
            }
        }

        return $row;
    }
}

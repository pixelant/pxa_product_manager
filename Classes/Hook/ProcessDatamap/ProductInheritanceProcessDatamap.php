<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Hook\ProcessDatamap;

use Doctrine\DBAL\FetchMode;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
use Pixelant\PxaProductManager\Utility\TcaUtility;
use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataGroup\TcaDatabaseRecord;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Handles data inheritance for products, copying inherited field data from parent to child, etc.
 */
class ProductInheritanceProcessDatamap
{
    protected const TABLE = 'tx_pxaproductmanager_domain_model_product';
    protected const RELATION_INDEX_TABLE = 'tx_pxaproductmanager_relation_inheritance_index';

    /**
     * The DataHandler object supplied when calling this class.
     *
     * @var DataHandler
     */
    protected DataHandler $dataHandler;

    /**
     * @var array Representation of DataHandler::datamap[tx_pxaproductmanager_domain_model_product].
     */
    protected array $productDatamap = [];

    /**
     * @var array Representation of DataHandler::datamap[tx_pxaproductmanager_domain_model_attributevalue].
     */
    protected array $attributeValueDatamap = [];

    /**
     * Cache of fields to inherit from a product with a specific product type. Key is: [productId]-[productTypeId].
     *
     * @var array
     */
    protected array $inheritedProductFieldsForProductType = [];

    /**
     * Cache of fields to inherit from a parent product's attribute values. Key is: [productId]-[attributeId].
     *
     * @var array
     */
    protected array $inheritedAttributeValuesForProduct = [];

    /**
     * A counter to keep track of how many products were given inherited data.
     *
     * Good for debugging and as a respectful gesture to the user who waits for 100K child products to be updated
     * because they corrected a typo.
     *
     * @var int
     */
    protected int $productsWithInheritedDataCount = 0;

    /**
     * APlaceholders (NEW01234567890abcdef) that will be put into the relation index when we have the actual UID.
     *
     * [
     *     'child' => <placeholder>,
     *     'parent' => <placeholder|UID>,
     *     'tablename' => <table name>
     * ]
     *
     * @var array
     */
    protected array $parentRelationPlaceholders = [];

    /**
     * Overlay parent product data as defined by the inherited fields in the ProductType.
     *
     * @param array $fieldArray
     * @param string $table
     * @param $id
     */

    // phpcs:ignore
    public function processDatamap_beforeStart(DataHandler $dataHandler): void
    {
        if (isset($dataHandler->datamap[ProductRepository::TABLE_NAME])) {
            $this->dataHandler = $dataHandler;
            $this->productDatamap = &$dataHandler->datamap[ProductRepository::TABLE_NAME];
            $this->attributeValueDatamap = &$dataHandler->datamap[AttributeValueRepository::TABLE_NAME];

            foreach (array_keys($this->productDatamap) as $identifier) {
                $this->processProductRecordOverlays($identifier);
            }

            if ($this->productsWithInheritedDataCount > 0) {
                $message = GeneralUtility::makeInstance(
                    FlashMessage::class,
                    sprintf(
                        $this->getLanguageService()->sL(
                            'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf'
                            . ':formengine.productinheritance.updatedcount'
                        ),
                        $this->productsWithInheritedDataCount
                    ),
                    '',
                    FlashMessage::INFO,
                    true
                );
                /** @var FlashMessageService $flashMessageService */
                $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
                $flashMessageService->getMessageQueueByIdentifier()->enqueue($message);
            }
        }
    }

    /**
     * Hook to replace NEW01234567890abcdef placeholders in relation index.
     *
     * @param DataHandler $dataHandler
     */

    // phpcs:ignore
    public function processDatamap_afterAllOperations(DataHandler $dataHandler): void
    {
        foreach ($this->parentRelationPlaceholders as $key => $value) {
            if (in_array($value['parent'], array_keys($dataHandler->substNEWwithIDs), true)) {
                $value['parent']
                    = $dataHandler->substNEWwithIDs[$value['parent']];
            }

            if (in_array($value['child'], array_keys($dataHandler->substNEWwithIDs), true)) {
                $value['child']
                    = $dataHandler->substNEWwithIDs[$value['child']];
            }

            $this->parentRelationPlaceholders[$key] = $value;
        }

        foreach ($this->parentRelationPlaceholders as $value) {
            if (
                MathUtility::canBeInterpretedAsInteger($value['parent'])
                && MathUtility::canBeInterpretedAsInteger($value['child'])
            ) {
                $this->addParentChildRelationToIndex(
                    (int)$value['parent'],
                    (int)$value['child'],
                    $value['tablename'],
                    (int)$value['child_parent_id'],
                    $value['child_parent_table'],
                );
            }
        }
    }

    /**
     * Recursively updates a product and its children with inherited data from the respective parent products.
     *
     * @param $identifier
     */
    protected function processProductRecordOverlays($identifier): void
    {
        $productRow = $this->productDatamap[$identifier];

        if (!is_array($productRow)) {
            $productRow = BackendUtility::getRecord(
                ProductRepository::TABLE_NAME,
                $identifier,
                'parent,product_type'
            );
        }

        // Relations could be using the formula `[tablename]_[id]`.
        // Datamap keys are string, so we need this value as string.
        $parentProductId = (string)array_pop(explode('_', (string)$productRow['parent'] ?? ''));
        // The product type will always be an integer. It is never new.
        $productTypeId = (int)array_pop(explode('_', (string)$productRow['product_type'] ?? ''));

        // If this is a new record, check if the product type is defined in the parent
        if (!MathUtility::canBeInterpretedAsInteger($identifier) && !$productTypeId && $parentProductId) {
            if (isset($this->productDatamap[$parentProductId]['product_type'])) {
                $productTypeId = (int)$this->productDatamap[$parentProductId]['product_type'];
            } else {
                $productTypeId = (int)BackendUtility::getRecord(
                    ProductRepository::TABLE_NAME,
                    $parentProductId,
                    'product_type'
                )['product_type'];
            }
        }

        if ($parentProductId && $productTypeId) {
            // get parent overlay data
            $parentProductOverlayData = $this->getParentProductOverlayData(
                (int)$parentProductId,
                (int)$productTypeId
            );
            // set pid of current product, seems to be "lost" though, but DataHandler requires a pid on insert
            $pid = BackendUtility::getRecord(ProductRepository::TABLE_NAME, $identifier, 'pid')['pid'] ?? 0;

            // Process normal product fields
            $productRowWithParentOverlay = array_merge(
                $productRow,
                $parentProductOverlayData
            );

            // process overlay relations (inline), only fields from parentProductOverlayData
            foreach ($parentProductOverlayData as $field => $value) {
                $productRowWithParentOverlay[$field] = $this->processOverlayRelations(
                    ProductRepository::TABLE_NAME,
                    $field,
                    $productRow,
                    (string)$value,
                    (string)$identifier,
                    (int)$pid
                );
            }

            if (count($parentProductOverlayData) > 0) {
                $this->productsWithInheritedDataCount++;
            }

            $this->productDatamap[$identifier] = $productRowWithParentOverlay;
        }

        $children = [];

        // Get child products if this product is not new. New products don't have children yet.
        if (MathUtility::canBeInterpretedAsInteger($identifier)) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable(ProductRepository::TABLE_NAME)
                ->createQueryBuilder();

            $queryBuilder
                ->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

            $children = $queryBuilder
                ->select('uid')
                ->from(ProductRepository::TABLE_NAME)
                ->where($queryBuilder->expr()->eq(
                    'parent',
                    $queryBuilder->createNamedParameter($identifier)
                ))
                ->execute()
                ->fetchAll(FetchMode::COLUMN, 0);
        }

        foreach ($this->productDatamap as $childIdentifier => $childData) {
            if ($childData['parent'] === ProductRepository::TABLE_NAME . '_' . $identifier) {
                $children[] = $childIdentifier;
            }
        }

        $children = array_unique($children);

        foreach ($children as $child) {
            $this->processProductRecordOverlays($child);
        }
    }

    /**
     * Returns an array of properties that should be overlaid upon any child products of $parent.
     *
     * @param int $parent
     * @param int $productType
     * @return array
     */

    // phpcs:disable Generic.Metrics.CyclomaticComplexity
    protected function getParentProductOverlayData(int $parent, int $productType): array
    {
        if (isset($this->inheritedProductFieldsForProductType[$parent . '-' . $productType])) {
            return $this->inheritedProductFieldsForProductType[$parent . '-' . $productType];
        }

        $inheritedFields = DataInheritanceUtility::getInheritedFieldsForProductType($productType);

        if (count($inheritedFields) === 0) {
            return [];
        }

        $inheritedFields[] = 'attributes_values';

        $parentRecord = $this->productDatamap[$parent];

        if (!is_array($parentRecord)) {
            $parentRecord = $this->compileRecordData(ProductRepository::TABLE_NAME, (int)$parent);
        }

        $overlayFields = [];

        foreach ($inheritedFields as $inheritedField) {
            // Don't handle attributes here
            if (strpos($inheritedField, 'attribute.') !== false) {
                continue;
            }

            $overlayFields[$inheritedField] = $parentRecord[$inheritedField];
        }

        $this->inheritedProductFieldsForProductType[$parent . '-' . $productType] = $overlayFields;

        return $overlayFields;
    }

    /**
     * Compile a record, i.e. return the record with the data that would be saved with it in a form.
     *
     * @param string $tableName
     * @param int $vanillaUid
     * @param bool $removeUnprocessedColumns Remove columns that are not processed
     * @return mixed
     */
    protected function compileRecordData(string $tableName, int $vanillaUid, bool $removeUnprocessedColumns = false)
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

    /**
     * Process relations that need to be copied in order to create an overlay.
     *
     * @param string $table The table name for the field
     * @param string $field The field name in the table
     * @param array $row The value of the field in the child record
     * @param string|null $parentValue The value of the field in the parent record
     * @param string $identifier The identifier of the current record (int cast to string or NEW0123456789abcdef)
     * @param int|null $pid The value of the pid of the related record
     * @return string
     */
    protected function processOverlayRelations(
        string $table,
        string $field,
        array $row,
        ?string $parentValue,
        string $identifier,
        ?int $pid
    ) {
        if ($parentValue === null) {
            return null;
        }

        $value = (string)$row[$field];

        $fieldTcaConfiguration = TcaUtility::getTcaFieldConfigurationAndRespectColumnsOverrides(
            $table,
            $field,
            $row
        );

        // Only inline relations need special treatment
        if ($fieldTcaConfiguration['type'] !== 'inline') {
            return $parentValue;
        }

        $foreignTable = $fieldTcaConfiguration['foreign_table'];

        $parentRelations = ArrayUtility::removeArrayEntryByValue(
            explode(',', $parentValue),
            ''
        );

        // Determine child relations
        if ($value !== '') {
            $childRelations = ArrayUtility::removeArrayEntryByValue(
                explode(',', $value),
                ''
            );
        } elseif (MathUtility::canBeInterpretedAsInteger($identifier)) {
            /** @var RelationHandler $relationHandler */
            $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);
            $relationHandler->start(
                '',
                $foreignTable,
                '',
                $identifier,
                $table,
                $fieldTcaConfiguration
            );

            $childRelations = array_column(
                $relationHandler->getFromDB()[$foreignTable] ?? [],
                'uid'
            ) ?? [];
        }

        // Check for deleted relations
        foreach ($childRelations as &$childRelation) {
            // Only allow integer keys. NEW0123456789abcdef keys can't have been deleted
            if (MathUtility::canBeInterpretedAsInteger($childRelation)) {
                $parentRelationUid = $this->findParentRelationUidInIndex(
                    (int)$childRelation,
                    $foreignTable,
                    (int)$identifier,
                    $table
                );

                // Delete any relation that is not present in the parent
                if (
                    (
                        $parentRelationUid === 0
                        || !in_array((string)$parentRelationUid, $parentRelations, true)
                    )
                    && isset($this->dataHandler->cmdmap[$foreignTable][(int)$parentRelationUid]['delete'])
                    && (int)$this->dataHandler->cmdmap[$foreignTable][(int)$parentRelationUid]['delete'] === 1
                ) {
                    $this->dataHandler->cmdmap[$foreignTable][(int)$childRelation]['delete'] = 1;
                    unset($childRelation);
                    $this->removeParentRelationsFromIndex(
                        $parentRelationUid,
                        $foreignTable,
                        (int)$identifier,
                        $table
                    );
                }
            }
        }

        $childRelations = [];
        // Make copies of new relations (child has to have its own relation record)
        foreach ($parentRelations as $parentRelationIdentifier) {
            if (is_string($parentRelationIdentifier) && strpos($parentRelationIdentifier, 'NEW') !== false) {
                $childRelationIdentifier = StringUtility::getUniqueId('NEW');

                $this->parentRelationPlaceholders[] = [
                    'child' => $childRelationIdentifier,
                    'parent' => $parentRelationIdentifier,
                    'tablename' => $foreignTable,
                    'child_parent_id' => $identifier,
                    'child_parent_table' => $table,
                ];
            // Parent relation isn't new
            } else {
                $childRelationIdentifier = (string)$this->findChildRelationUidInIndex(
                    (int)$parentRelationIdentifier,
                    $foreignTable,
                    (int)$identifier,
                    $table
                );

                // Child relation is new
                if (!$childRelationIdentifier) {
                    $childRelationIdentifier = StringUtility::getUniqueId('NEW');

                    $this->parentRelationPlaceholders[] = [
                        'child' => $childRelationIdentifier,
                        'parent' => $parentRelationIdentifier,
                        'tablename' => $foreignTable,
                        'child_parent_id' => $identifier,
                        'child_parent_table' => $table,
                    ];
                }
            }

            $childRecord = $this->dataHandler->datamap[$foreignTable][$parentRelationIdentifier];
            if (empty($childRecord)) {
                // Try fetch, if parent product isn't saved datamap for it is missing
                // Need to define how to know what data to fetch
            }

            if (!empty($childRecord)) {
                // If the record only has the "hidden" field set, it's an unexpanded inline record and we must expand it
                if (
                    strpos($parentRelationIdentifier, 'NEW') === false
                    && count($childRecord) === 1
                    && isset($childRecord['hidden'])
                ) {
                    $compiledParentRecord = $this->compileRecordData($foreignTable, (int)$parentRelationIdentifier, true);
                    $this->dataHandler->datamap[$foreignTable][$parentRelationIdentifier] = $compiledParentRecord;
                    $childRecord = $compiledParentRecord;
                }

                // Set the type field value if it isn't already set
                $typeField = $GLOBALS['TCA'][$foreignTable]['ctrl']['type'];
                if (
                    isset($typeField)
                    && strpos($typeField, ':') === false // Skip these types e.g. in sys_file_reference
                    && !isset($childRecord[$typeField])
                    && MathUtility::canBeInterpretedAsInteger($parentRelationIdentifier)
                ) {
                    $typeValue = (string)BackendUtility::getRecord(
                        $foreignTable,
                        $parentRelationIdentifier,
                        $typeField
                    )[$typeField];

                    // Se the parent value too. We'll need it to be correct.
                    $this->dataHandler->datamap[$foreignTable][$parentRelationIdentifier][$typeField] = $typeValue;
                    $childRecord[$typeField] = $typeValue;
                }

                // if record is new, set pid, so it can be saved in DataHandler.
                if (!MathUtility::canBeInterpretedAsInteger($childRelationIdentifier)) {
                    $childRecord['pid'] = $pid;
                }

                $this->dataHandler->datamap[$foreignTable][$childRelationIdentifier] = $childRecord;

                // Recurse through inline relations for childrens' children
                foreach ($childRecord as $field => $value) {
                    $childRecord[$field] = $this->processOverlayRelations(
                        $foreignTable,
                        $field,
                        $childRecord,
                        (string)$this->dataHandler->datamap[$foreignTable][$parentRelationIdentifier][$field],
                        $childRelationIdentifier,
                        $pid
                    );
                }

                $this->dataHandler->datamap[$foreignTable][$childRelationIdentifier] = $childRecord;

                $childRelations[] = $childRelationIdentifier;
            }
        }

        return implode(',', $childRelations);
    }

    /**
     * Get the LanguageService.
     *
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

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
    protected function findParentRelationUidInIndex(
        int $childUid,
        string $tablename,
        int $childParentId,
        string $childParentTable
    ): int
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::RELATION_INDEX_TABLE);

        return (int)$queryBuilder
            ->select('uid_parent')
            ->from(self::RELATION_INDEX_TABLE)
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
    protected function findChildRelationUidInIndex(
        int $parentUid,
        string $tablename,
        int $childParentId,
        string $childParentTable
    ): int
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::RELATION_INDEX_TABLE);

        return (int)$queryBuilder
            ->select('uid_child')
            ->from(self::RELATION_INDEX_TABLE)
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
    protected function addParentChildRelationToIndex(
        int $parentUid,
        int $childUid,
        string $tablename,
        int $childParentId,
        string $childParentTable
    ): void
    {
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
            ->getQueryBuilderForTable(self::RELATION_INDEX_TABLE);

        $queryBuilder
            ->insert(self::RELATION_INDEX_TABLE)
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
    protected function removeParentRelationsFromIndex(
        int $parentUid,
        string $tablename,
        int $childParentId,
        string $childParentTable
    ): void
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::RELATION_INDEX_TABLE);

        $queryBuilder
            ->delete(self::RELATION_INDEX_TABLE)
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

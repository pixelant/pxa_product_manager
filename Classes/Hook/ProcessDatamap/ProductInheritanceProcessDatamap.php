<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Hook\ProcessDatamap;

use Doctrine\DBAL\FetchMode;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Domain\Repository\RelationInheritanceIndexRepository;
use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
use Pixelant\PxaProductManager\Utility\RelationInheritanceIndexUtility;
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
    protected const TABLE = ProductRepository::TABLE_NAME;

    /**
     * @var RelationInheritanceIndexRepository
     */
    protected RelationInheritanceIndexRepository $relationInheritanceIndexRepository;

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
     * A array to keep track of what products were given inherited data.
     *
     * Used as counter and to check for invalid relations if parent product have changed.
     *
     * Good for debugging and as a respectful gesture to the user who waits for 100K child products to be updated
     * because they corrected a typo.
     *
     * @var int
     */
    protected array $productsWithInheritedData = [];

    /**
     * APlaceholders (NEW01234567890abcdef) that will be put into the relation index when we have the actual UID.
     *
     * [
     *     'child' => <placeholder>,
     *     'parent' => <placeholder|UID>,
     *     'tablename' => <table name>,
     *     'child_parent_id' => <child parent id>,
     *     'child_parent_table' => <childs parent able name>
     * ]
     *
     * @var array
     */
    protected array $parentRelationPlaceholders = [];

    /**
     * Array to store product relations.
     *
     * @var array
     */
    protected array $productRelations = [];

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->relationInheritanceIndexRepository = GeneralUtility::makeInstance(
            RelationInheritanceIndexRepository::class
        );
    }

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

            foreach (array_keys($this->productDatamap) as $identifier) {
                $this->processProductRecordOverlays($identifier);
            }

            if (count($this->productsWithInheritedData) > 0) {
                $message = GeneralUtility::makeInstance(
                    FlashMessage::class,
                    sprintf(
                        $this->getLanguageService()->sL(
                            'LLL:EXT:pxa_product_manager/Resources/Private/Language/locallang_be.xlf'
                            . ':formengine.productinheritance.updatedcount'
                        ),
                        count($this->productsWithInheritedData),
                        implode(',', $this->productsWithInheritedData)
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

            if (in_array($value['child_parent_id'], array_keys($dataHandler->substNEWwithIDs), true)) {
                $value['child_parent_id']
                    = $dataHandler->substNEWwithIDs[$value['child_parent_id']];
            }

            $this->parentRelationPlaceholders[$key] = $value;
        }

        foreach ($this->parentRelationPlaceholders as $value) {
            if (
                MathUtility::canBeInterpretedAsInteger($value['parent'])
                && MathUtility::canBeInterpretedAsInteger($value['child'])
            ) {
                $this->relationInheritanceIndexRepository->addParentChildRelationToIndex(
                    (int)$value['parent'],
                    (int)$value['child'],
                    $value['tablename'],
                    (int)$value['child_parent_id'],
                    $value['child_parent_table'],
                );
            }
        }

        // Make sure relation inheritance index and attribute values are up to date for affected child products.
        if (count($this->productsWithInheritedData) > 0) {
            foreach ($this->productsWithInheritedData as $childProductUid) {
                RelationInheritanceIndexUtility::updateRelationsByChildParentId((int)$childProductUid);
            }
        }
    }

    /**
     * Recursively updates a product and its children with inherited data from the respective parent products.
     *
     * @param $identifier
     */

    // phpcs:disable Generic.Metrics.CyclomaticComplexity
    protected function processProductRecordOverlays($identifier): void
    {
        $inheritMode = false;
        $productRow = $this->productDatamap[$identifier];

        // $productRow isn't an array if parent product was saved and then
        // called function to save children, then inheritmode = true
        if (!is_array($productRow)) {
            $inheritMode = true;
            $productRow = [];
        }

        // Make sure productRow have product_type and parent.
        $this->addProductTypeAndParentToRecordIfNeeded($identifier, $productRow);

        // The product type will always be an integer. It is never new.
        $productTypeId = (int)array_pop(explode('_', (string)$productRow['product_type'] ?? ''));

        // Fetch inherited fields by product_type.
        $inheritedFields = $this->getInheritedFieldsForProductType($productTypeId);

        // No need to continue if product_type has no inherited fields.
        if (empty($inheritedFields)) {
            return;
        }

        $recordIsNew = !MathUtility::canBeInterpretedAsInteger($identifier);

        // Relations could be using the formula `[tablename]_[id]`.
        // Datamap keys are string, so we need this value as string.
        $parentProductId = (string)array_pop(explode('_', (string)$productRow['parent'] ?? ''));

        $children = $this->fetchChildRecordIdentifiers($identifier);
        $recordIsParent = count($children) > 0;
        $recordIsChild = !empty($parentProductId);

        // If data doesn't contain any inherited fields (e.g. saved in list mode)
        // and we aren't processing a child when the parent was saved,
        // and we aren't processing a new child
        // we do not need to continue.
        if (
            empty(array_intersect(array_keys($productRow), $inheritedFields))
            && !$inheritMode
            && !($recordIsNew && $recordIsChild)
        ) {
            return;
        }

        $status = '';
        // Current record is a "child".
        if ($recordIsChild) {
            // Get parent overlay data.
            $parentProductOverlayData = $this->getParentProductOverlayData(
                (int)$parentProductId,
                (int)$productTypeId
            );

            // Load product relations
            $this->loadProductRelations(
                $identifier,
                $productRow,
                array_keys($parentProductOverlayData),
            );

            // Set pid of current product, seems to be "lost" though, but DataHandler requires a pid on insert.
            $pid = BackendUtility::getRecord(ProductRepository::TABLE_NAME, $identifier, 'pid')['pid'] ?? 0;

            // Current record is updated when parent was saved. (can't be a new record)
            if (!$inheritMode) {
                // Fetch product relations according parent product overlay data.
                $this->loadProductRelations(
                    $parentProductId,
                    [],
                    array_keys($parentProductOverlayData),
                );
            }

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
                    (int)$pid,
                    $inheritedFields
                );
            }

            if (count($parentProductOverlayData) > 0) {
                $this->productsWithInheritedData[] = $identifier;
            }

            $this->productDatamap[$identifier] = $productRowWithParentOverlay;
        }

        // Current record is a updated "parent".
        if ($recordIsParent && !$recordIsNew) {
            // Process children.
            // Not sure when this can happen (from before changes)?
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

        $inheritedFields = $this->getInheritedFieldsForProductType($productType);

        if (count($inheritedFields) === 0) {
            return [];
        }

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
     * @param array $inheritedFields The value of the pid of the related record
     * @return string
     */
    protected function processOverlayRelations(
        string $table,
        string $field,
        array $row,
        ?string $parentValue,
        string $identifier,
        ?int $pid,
        array $inheritedFields
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
        if (is_array($childRelations)) {
            foreach ($childRelations as &$childRelation) {
                // Only allow integer keys. NEW0123456789abcdef keys can't have been deleted
                if (MathUtility::canBeInterpretedAsInteger($childRelation)) {
                    $parentRelationUid = $this->relationInheritanceIndexRepository->findParentRelationUidInIndex(
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
                        $this->relationInheritanceIndexRepository->removeParentRelationsFromIndex(
                            $parentRelationUid,
                            $foreignTable,
                            (int)$identifier,
                            $table
                        );
                    }
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
                $childRelationIdentifier
                    = (string)$this->relationInheritanceIndexRepository->findChildRelationUidInIndex(
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
                $childRecord = $this->productRelations[$foreignTable][$parentRelationIdentifier];
            }

            if (!empty($childRecord)) {
                // If the record only has the "hidden" field set, it's an unexpanded inline record and we must expand it
                if (
                    strpos($parentRelationIdentifier, 'NEW') === false
                    && count($childRecord) === 1
                    && isset($childRecord['hidden'])
                ) {
                    $compiledParentRecord = $this->compileRecordData(
                        $foreignTable,
                        (int)$parentRelationIdentifier,
                        true
                    );
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

                // Only specific attribute values should be inherited.
                if ($typeField === 'attribute' && isset($childRecord[$typeField])) {
                    // If typeField.typeValue isn't in inheritedFields use own value.
                    if (!in_array($typeField . '.' . $childRecord[$typeField], $inheritedFields, true)) {
                        if (isset($this->dataHandler->datamap[$foreignTable][$childRelationIdentifier]['value'])) {
                            $childRecord['value']
                                = $this->dataHandler->datamap[$foreignTable][$childRelationIdentifier]['value'];
                        } else {
                            $childRecord['value']
                                = $this->productRelations[$foreignTable][$childRelationIdentifier]['value'] ?? '';
                        }
                    }
                }

                // if record is new, set pid, so it can be saved in DataHandler.
                if (!MathUtility::canBeInterpretedAsInteger($childRelationIdentifier)) {
                    $childRecord['pid'] = $pid;
                }

                $this->dataHandler->datamap[$foreignTable][$childRelationIdentifier] = $childRecord;

                if (isset($this->dataHandler->datamap[$foreignTable][$parentRelationIdentifier][$field])) {
                    // Recurse through inline relations for childrens' children
                    foreach ($childRecord as $field => $value) {
                        $childRecord[$field] = $this->processOverlayRelations(
                            $foreignTable,
                            $field,
                            $childRecord,
                            (string)$this->dataHandler->datamap[$foreignTable][$parentRelationIdentifier][$field],
                            $childRelationIdentifier,
                            $pid,
                            $inheritedFields
                        );
                    }

                    $this->dataHandler->datamap[$foreignTable][$childRelationIdentifier] = $childRecord;
                }

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
     * Add product_type and parent to current record if missing and it can be fetched.
     *
     * @param $identifier
     * @param array $record
     * @return void
     */
    protected function addProductTypeAndParentToRecordIfNeeded($identifier, array &$record): void
    {
        // If parent isn't set on update, fetch from record, not changed?
        if (MathUtility::canBeInterpretedAsInteger($identifier)) {
            if (!isset($record['parent']) || empty($record['product_type'])) {
                $productRow = BackendUtility::getRecord(
                    ProductRepository::TABLE_NAME,
                    $identifier,
                    'parent, product_type'
                );

                if (isset($productRow['parent']) && !isset($record['parent'])) {
                    $record['parent'] = $productRow['parent'];
                }
            }
        }
        // If parent is set, fetch product_type from parent instead of relying on child.
        if (!empty($record['parent'])) {
            $parent = (string)array_pop(explode('_', (string)$record['parent'] ?? ''));
            $productRow = BackendUtility::getRecord(
                ProductRepository::TABLE_NAME,
                $parent,
                'product_type'
            );
        }

        if (isset($productRow['product_type']) && (int)$productRow['product_type'] !== (int)($record['product_type'])) {
            $record['product_type'] = (string)$productRow['product_type'];
        }
    }

    /**
     * Fetch child products.
     *
     * @param $identifier
     * @return array
     */
    protected function fetchChildRecordIdentifiers($identifier): array
    {
        // New records can't have any child records yet.
        if (MathUtility::canBeInterpretedAsInteger($identifier)) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable(ProductRepository::TABLE_NAME)
                ->createQueryBuilder();

            $queryBuilder
                ->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

            return $queryBuilder
                ->select('uid')
                ->from(ProductRepository::TABLE_NAME)
                ->where($queryBuilder->expr()->eq(
                    'parent',
                    $queryBuilder->createNamedParameter($identifier)
                ))
                ->execute()
                ->fetchAll(FetchMode::COLUMN, 0);
        }

        return [];
    }

    /**
     * Load product relations.
     *
     * @param $identifier
     * @param array $row
     * @param array $fields
     * @param string $relationTable
     * @return void
     */
    protected function loadProductRelations($identifier, $row, $fields): void
    {
        if (!empty($this->productRelations[$identifier])) {
            return;
        }

        // if row is empty initialize simple product row
        if (count($row) === 0) {
            $this->addProductTypeAndParentToRecordIfNeeded($identifier, $row);
        }

        foreach ($fields as $field) {
            $fieldTcaConfiguration = TcaUtility::getTcaFieldConfigurationAndRespectColumnsOverrides(
                self::TABLE,
                $field,
                $row
            );

            // Only inline relations need special treatment
            if ($fieldTcaConfiguration['type'] !== 'inline') {
                continue;
            }

            $foreignTable = $fieldTcaConfiguration['foreign_table'];

            /** @var RelationHandler $relationHandler */
            $relationHandler = GeneralUtility::makeInstance(RelationHandler::class);
            $relationHandler->start(
                '',
                $foreignTable,
                '',
                $identifier,
                self::TABLE,
                $fieldTcaConfiguration
            );

            $this->productRelations[$identifier][$foreignTable] = $relationHandler->getFromDB()[$foreignTable] ?? [];

            // load compiled record data
            foreach ($this->productRelations[$identifier][$foreignTable] as $id => $values) {
                $this->productRelations[$foreignTable][$id]
                    = $this->compileRecordData($foreignTable, (int)$id, true);
            }
        }
    }

    /**
     * Local get inherited fields for product type.
     * Adds 'attributes_values' field if any attribute is inherited.
     *
     * @param int $productType
     * @return array
     */
    protected function getInheritedFieldsForProductType(int $productType): array
    {
        $inheritedFields = DataInheritanceUtility::getInheritedFieldsForProductType($productType) ?? [];
        foreach ($inheritedFields as $inheritedField) {
            if (strpos($inheritedField, 'attribute.') !== false) {
                $inheritedFields[] = 'attributes_values';

                break;
            }
        }

        return $inheritedFields;
    }
}

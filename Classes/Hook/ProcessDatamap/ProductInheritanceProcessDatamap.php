<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Hook\ProcessDatamap;

use Doctrine\DBAL\FetchMode;
use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
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
     * Cache of fields to inherit from a product with a specific product type. Key is: [productId]-[productTypeId].
     *
     * @var array
     */
    protected array $inheritedProductFieldsForProductType = [];

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

    /** @codingStandardsIgnoreStart */
    public function processDatamap_beforeStart(DataHandler $dataHandler): void
    {// @codingStandardsIgnoreEnd
        if (isset($dataHandler->datamap[self::TABLE])) {
            $this->dataHandler = $dataHandler;
            $this->productDatamap = $dataHandler->datamap[self::TABLE];

            foreach (array_keys($this->productDatamap) as $identifier) {
                $this->processRecordOverlays($identifier);
            }

            $dataHandler->datamap[self::TABLE] = $this->productDatamap;

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

    /** @codingStandardsIgnoreStart */
    public function processDatamap_afterAllOperations(DataHandler $dataHandler): void
    {// @codingStandardsIgnoreEnd
        foreach ($this->parentRelationPlaceholders as &$parentRelationPlaceholder) {
            if (in_array($parentRelationPlaceholder['parent'], array_keys($this->dataHandler->substNEWwithIDs), true)) {
                $parentRelationPlaceholder['parent'] =
                    $this->dataHandler->substNEWwithIDs[$parentRelationPlaceholder['parent']];
            }

            if (in_array($parentRelationPlaceholder['child'], array_keys($this->dataHandler->substNEWwithIDs), true)) {
                $parentRelationPlaceholder['child'] =
                    $this->dataHandler->substNEWwithIDs[$parentRelationPlaceholder['child']];
            }
        }

        foreach ($this->parentRelationPlaceholders as $parentRelationPlaceholder) {
            $this->addParentChildRelationToIndex(
                $parentRelationPlaceholder['parent'],
                $parentRelationPlaceholder['child'],
                $parentRelationPlaceholder['tablename']
            );
        }
    }

    /**
     * Recursively updates a product and its children with inherited data from the respective parent products.
     *
     * @param $identifier
     */
    protected function processRecordOverlays($identifier): void
    {
        $row = $this->productDatamap[$identifier];

        // Relations are using the formula [tablename]_[id]
        $parentId = array_pop(explode('_', $row['parent'] ?? ''));

        if (!is_array($row)) {
            $row = BackendUtility::getRecord(
                self::TABLE,
                $identifier,
                'parent,product_type'
            );

            $parentId = $row['parent'];
        }

        if ($row['parent'] && $row['product_type']) {
            $productType = $row['product_type'] ?? BackendUtility::getRecord(
                self::TABLE,
                $parentId,
                'product_type'
            )['product_type'];

            if ($productType) {
                $parentOverlayRow = array_merge($row, $this->getParentOverlayData((int)$parentId, (int)$productType));

                foreach ($parentOverlayRow as $field => $value) {
                    $fieldTcaConfiguration = BackendUtility::getTcaFieldConfiguration(self::TABLE, $field);
                    if ($fieldTcaConfiguration['type'] === 'inline') {
                        $foreignTable = $fieldTcaConfiguration['foreign_table'];

                        $parentRelations = ArrayUtility::removeArrayEntryByValue(
                            explode(',', $parentOverlayRow[$field]),
                            ''
                        );

                        if (isset($row['field'])) {
                            $childRelations = ArrayUtility::removeArrayEntryByValue(
                                explode(',', $row[$field]),
                                ''
                            );
                        } else {
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

                            $childRelations = array_column(
                                $relationHandler->getFromDB()[$foreignTable] ?? [],
                                'uid'
                            );
                        }

                        // Check for deleted relations
                        foreach ($childRelations as $childRelation) {
                            $parentRelationUid = $this->findParentRelationUidInIndex($childRelation, $foreignTable);

                            // Delete any relation that is not present in the parent
                            if ($parentRelationUid === 0 || !in_array($parentRelationUid, $parentRelations, true)) {
                                $this->dataHandler->cmdmap[$foreignTable][(int)$childRelation]['delete'] = 1;
                                $this->removeParentRelationsFromIndex($parentRelationUid, $foreignTable);
                            }
                        }

                        // Make copies of new relations (child has to have its own relation record)
                        foreach ($parentRelations as &$parentRelation) {
                            if (is_string($parentRelation) && strpos($parentRelation, 'NEW') !== false) {
                                $newRelation = StringUtility::getUniqueId('NEW');
                                $this->dataHandler->datamap[$foreignTable][$newRelation] =
                                    $this->dataHandler->datamap[$foreignTable][$parentRelation];

                                $this->parentRelationPlaceholders[] = [
                                    'child' => $newRelation,
                                    'parent' => $parentRelation,
                                    'tablename' => $foreignTable,
                                ];

                                $parentRelation = $newRelation;
                            }
                        }

                        $parentOverlayRow[$field] = implode(',', $parentRelations);
                    }
                }

                $this->productDatamap[$identifier] = $parentOverlayRow;
            }
        }

        $children = [];

        if (MathUtility::canBeInterpretedAsInteger($identifier)) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable(self::TABLE)
                ->createQueryBuilder();

            $queryBuilder
                ->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

            $children = $queryBuilder
                ->select('uid')
                ->from(self::TABLE)
                ->where($queryBuilder->expr()->eq(
                    'parent',
                    $queryBuilder->createNamedParameter($identifier)
                ))
                ->execute()
                ->fetchAll(FetchMode::COLUMN, 0);
        }

        foreach ($this->productDatamap as $childIdentifier => $childData) {
            if ($childData['parent'] === self::TABLE . '_' . $identifier) {
                $children[] = $childIdentifier;
            }
        }

        $children = array_unique($children);

        foreach ($children as $child) {
            $this->processRecordOverlays($child);
        }
    }

    /**
     * Returns an array of properties that should be overlaid upon any child products of $parent.
     *
     * @param int $parent
     * @param int $productType
     * @return array
     */
    protected function getParentOverlayData(int $parent, int $productType): array
    {
        if (isset($this->inheritedProductFieldsForProductType[$parent . '-' . $productType])) {
            return $this->inheritedProductFieldsForProductType[$parent . '-' . $productType];
        }

        $inheritedFields = DataInheritanceUtility::getInheritedFieldsForProductType($productType);

        if (count($inheritedFields) === 0) {
            return [];
        }

        $parentRecord = $this->productDatamap[$parent];

        if (!is_array($parentRecord)) {
            $formDataGroup = GeneralUtility::makeInstance(TcaDatabaseRecord::class);
            $formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class, $formDataGroup);

            $formDataCompilerInput = [
                'tableName' => self::TABLE,
                'vanillaUid' => $parent,
                'command' => 'edit',
            ];

            $parentRecord = $formDataCompiler->compile($formDataCompilerInput)['databaseRow'];
        }

        $overlayFields = [];

        foreach ($inheritedFields as $inheritedField) {
            if (is_array($parentRecord[$inheritedField])) {
                $relations = [];

                foreach ($parentRecord[$inheritedField] as $item) {
                    if (is_array($item)) {
                        $relations[] = $item['table'] . '_' . $item['uid'];
                    } else {
                        $relations[] = $item;
                    }
                }

                $row[$inheritedField] = implode(',', $relations);

                continue;
            }

            $overlayFields[$inheritedField] = $parentRecord[$inheritedField];
        }

        $this->inheritedProductFieldsForProductType[$parent . '-' . $productType] = $overlayFields;

        if (count($overlayFields) > 0) {
            $this->productsWithInheritedDataCount++;
        }

        return $overlayFields;
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
     * Returns the UID of a relations's cousin (attached to a parent product).
     *
     * This is useful for inline relations, where a child product must retain its own copies of the relations and where
     * there is no way to know which relation on the child product is the copy of which relation on the parent. To avoid
     * deleting and recreating all relations on the child object, calling this function and
     * addParentChildRelationToIndex() we can keep a record of the relationships.
     *
     * @param int $childUid
     * @param string $tablename
     * @return int Uid of the record. Zero if not found
     */
    protected function findParentRelationUidInIndex(int $childUid, string $tablename): int
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::RELATION_INDEX_TABLE);

        return (int)$queryBuilder
            ->select('uid_parent')
            ->from(self::RELATION_INDEX_TABLE)
            ->where(
                $queryBuilder->expr()->eq('uid_child', $queryBuilder->createNamedParameter($childUid)),
                $queryBuilder->expr()->eq('tablename', $queryBuilder->createNamedParameter($tablename))
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
     */
    protected function addParentChildRelationToIndex(int $parentUid, int $childUid, string $tablename): void
    {
        if (
            $parentUid === 0
            || $childUid === 0
            || $this->findParentRelationUidInIndex($childUid, $tablename) === $parentUid
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
     */
    protected function removeParentRelationsFromIndex(int $parentUid, string $tablename): void
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable(self::RELATION_INDEX_TABLE);

        $queryBuilder
            ->delete(self::RELATION_INDEX_TABLE)
            ->where(
                $queryBuilder->expr()->eq('uid_parent', $queryBuilder->createNamedParameter($parentUid)),
                $queryBuilder->expr()->eq('tablename', $queryBuilder->createNamedParameter($tablename))
            )
            ->execute();
    }
}

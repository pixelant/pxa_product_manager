<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Hook;

use Doctrine\DBAL\FetchMode;
use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataGroup\TcaDatabaseRecord;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Handles data inheritance for products, copying inherited field data from parent to child, etc.
 */
class ProductInheritanceProcessDatamap
{
    protected const TABLE = 'tx_pxaproductmanager_domain_model_product';

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

        if ($row['parent']) {
            $productType = $row['product_type'] ?? BackendUtility::getRecord(
                self::TABLE,
                $parentId,
                'product_type'
            )['product_type'];

            if ($productType) {
                $this->productDatamap[$identifier] = $row + $this->getParentOverlayData($parentId, $productType);
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
}

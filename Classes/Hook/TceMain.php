<?php
namespace Pixelant\PxaProductManager\Hook;

use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Utility\MainUtility;
use Pixelant\PxaProductManager\Utility\TCAUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use Pixelant\PxaProductManager\Utility\ProductUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014
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

/**
 *
 *
 * @package pxa_products
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class TceMain
{
    /**
     * @param $fieldArray
     * @param $table
     * @param $id
     * @param $reference
     */
    // @codingStandardsIgnoreStart
    public function processDatamap_preProcessFieldArray(&$fieldArray, $table, $id, /** @noinspection PhpUnusedParameterInspection */ $reference)
    {// @codingStandardsIgnoreEnd
        if ($table === 'tx_pxaproductmanager_domain_model_product'
            && MathUtility::canBeInterpretedAsInteger($id)
        ) {
            $productData = [];
            $files = [];

            foreach ($fieldArray as $fieldName => $value) {
                if (TCAUtility::isAttributeField($fieldName)) {
                    $attributeId = TCAUtility::determinateAttributeUidFromFieldName($fieldName);
                    $productData[$attributeId] = $value;
                    unset($fieldArray[$fieldName]);
                } elseif (TCAUtility::isFalAttributeField($fieldName)) {
                    $files[] = $value;
                    unset($fieldArray[$fieldName]);
                }
            }

            if (!empty($files)) {
                $fieldArray[TCAUtility::ATTRIBUTE_FAL_FIELD_NAME] = implode(',', $files);
            }

            if (!empty($productData)) {
                $fieldArray['serialized_attributes_values'] = serialize($productData);
                $this->updateAttributeValues($id, $productData, $fieldArray);
            }
        }
    }

    /**
     * Update attributes
     *
     * @param int $productUid
     * @param array $productData
     * @param array $fieldArray
     */
    protected function updateAttributeValues(int $productUid, array $productData, array $fieldArray)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
            'tx_pxaproductmanager_domain_model_attributevalue'
        );

        $statement = $queryBuilder
            ->select('uid', 'value', 'attribute')
            ->from('tx_pxaproductmanager_domain_model_attributevalue')
            ->where(
                $queryBuilder->expr()->eq('product', $queryBuilder->createNamedParameter(
                    $productUid,
                    Connection::PARAM_INT
                )),
                $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter(
                    $fieldArray['sys_language_uid'],
                    Connection::PARAM_INT
                ))
            )
            ->execute();

        $existForAttributes = [];
        $processedAttributes = [];

        while ($attributeValue = $statement->fetch()) {
            // found attribute
            if (array_key_exists($attributeValue['attribute'], $productData)
                && !in_array($attributeValue['attribute'], $processedAttributes)) {
                $existForAttributes[] = (int)$attributeValue['attribute'];

                if ($attributeValue['value'] != $productData[$attributeValue['attribute']]) {
                    // Update value
                    $queryBuilder
                        ->update('tx_pxaproductmanager_domain_model_attributevalue')
                        ->where(
                            $queryBuilder->expr()->eq(
                                'uid',
                                $queryBuilder->createNamedParameter($attributeValue['uid'])
                            )
                        )
                        ->set('value', $productData[$attributeValue['attribute']])
                        ->execute();
                }
            } else {
                // remove
                $queryBuilder
                    ->delete('tx_pxaproductmanager_domain_model_attributevalue')
                    ->where(
                        $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($attributeValue['uid']))
                    )
                    ->execute();
            }
            $processedAttributes[] = $attributeValue['attribute'];
        }

        $needToCreateValuesFor = array_diff(
            array_keys($productData),
            $existForAttributes
        );

        if (!empty($needToCreateValuesFor)) {
            $rows = [];
            $time = time();

            foreach ($needToCreateValuesFor as $attributeUid) {
                $rows[] = [
                    'attribute' => $attributeUid,
                    'product' => $productUid,
                    'value' => $productData[$attributeUid],
                    'tstamp' => $time,
                    'crdate' => $time,
                    'pid' => $this->getPid($productUid, $fieldArray),
                    't3_origuid' => 0,
                    'l10n_parent' => 0,
                    'sys_language_uid' => intval($fieldArray['sys_language_uid'])
                ];
            }

            /** @var Connection $connection */
            $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(
                'tx_pxaproductmanager_domain_model_attributevalue'
            );
            $connection->bulkInsert(
                'tx_pxaproductmanager_domain_model_attributevalue',
                $rows,
                [
                    'attribute',
                    'product',
                    'value',
                    'tstamp',
                    'crdate',
                    'pid',
                    't3_origuid',
                    'l10n_parent',
                    'sys_language_uid'
                ],
                [
                    \PDO::PARAM_INT,
                    \PDO::PARAM_INT,
                    \PDO::PARAM_STR,
                    \PDO::PARAM_INT,
                    \PDO::PARAM_INT,
                    \PDO::PARAM_INT,
                    \PDO::PARAM_INT,
                    \PDO::PARAM_INT,
                    \PDO::PARAM_INT
                ]
            );
        }
    }

    /**
     * Get pid
     *
     * @param mixed $id Either the record UID or a string if a new record has been created
     * @param array $fieldArray The record row how it has been inserted into the database
     * @return int
     */
    protected function getPid($id, $fieldArray = [])
    {
        // New records, or when pid is changed it is in the fieldArray array
        if (MathUtility::canBeInterpretedAsInteger($fieldArray['pid'])) {
            $pid = $fieldArray['pid'];
        }

        // Update of record, get pid from database,
        // id should be integer and not "NEWXXXX" because new records should have pid in $fieldArray
        if (!isset($pid) && MathUtility::canBeInterpretedAsInteger($id)) {
            $rec = BackendUtility::getRecord(
                'tx_pxaproductmanager_domain_model_product',
                $id,
                'pid',
                '',
                false
            ); // Don't respect delete clause
            if (MathUtility::canBeInterpretedAsInteger($rec['pid'])) {
                $pid = $rec['pid'];
            }
        }

        return $pid ?? 1;
    }

    /**
     * Set custom sorting for product
     *
     * @param $status
     * @param $table
     * @param $id
     * @param $fieldArray
     * @param $pObj
     */
    // @codingStandardsIgnoreStart
    public function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $pObj)
    {// @codingStandardsIgnoreEnd
        if ($table == 'tx_pxaproductmanager_domain_model_product') {
            /** @var ProductRepository $productRepository */
            $productRepository = MainUtility::getObjectManager()->get(ProductRepository::class);

            /** @var Product $product */
            $product = $productRepository->findByIdentifier($id);

            if ($product) {
                $product->setCustomSorting(ProductUtility::getCalculatedCustomSorting($product));

                if ($product->_isDirty()) {
                    $productRepository->update($product);

                    /** @var PersistenceManager $persistenceManager */
                    $persistenceManager = MainUtility::getObjectManager()->get(PersistenceManager::class);
                    $persistenceManager->persistAll();
                }
            }
        }
    }
}

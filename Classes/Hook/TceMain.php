<?php
namespace Pixelant\PxaProductManager\Hook;

use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Utility\MainUtility;
use Pixelant\PxaProductManager\Utility\TCAUtility;
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
                $fieldArray[TCAUtility::ATTRIBUTES_VALUES_FIELD_NAME] = json_encode($productData);
            }
        }
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

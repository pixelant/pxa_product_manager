<?php

namespace Pixelant\PxaProductManager\Hook\ProcessDatamap;

use Pixelant\PxaProductManager\Attributes\ValueUpdater\UpdaterInterface;
use Pixelant\PxaProductManager\Utility\AttributeTcaNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/*
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
 */

/**
 * TCA hook that will update attributes values table before product save.
 */
class UpdateAttributeValuesProcessDatamap
{
    /**
     * Save attributes values and remove from fields array.
     *
     * @param array $fieldArray
     * @param string $table
     * @param $id
     */

    /** @codingStandardsIgnoreStart */
    public function processDatamap_preProcessFieldArray(array &$fieldArray, string $table, $id): void
    {// @codingStandardsIgnoreEnd
        if (
            $table === 'tx_pxaproductmanager_domain_model_product'
            && MathUtility::canBeInterpretedAsInteger($id)
        ) {
            $updater = $this->getUpdaterService();
            $files = [];

            foreach ($fieldArray as $fieldName => $value) {
                if (AttributeTcaNamingUtility::isAttributeFieldName($fieldName)) {
                    // Save files to separate array
                    if (AttributeTcaNamingUtility::isFileAttributeFieldName($fieldName)) {
                        $attributeId = AttributeTcaNamingUtility::extractIdFromFieldName($fieldName);
                        $attributeFiles = GeneralUtility::trimExplode(',', $value, true);
                        $updater->update($id, $attributeId, count($attributeFiles));
                        $files[] = $value;
                    } else {
                        $attributeId = AttributeTcaNamingUtility::extractIdFromFieldName($fieldName);
                        $updater->update($id, $attributeId, $value);
                    }

                    // Remove from array
                    unset($files[$fieldName]);
                }
            }

            if (!empty($files)) {
                $fieldArray[AttributeTcaNamingUtility::FAL_DB_FIELD] = implode(',', $files);
            }
        }
    }

    /**
     * @return UpdaterInterface
     */
    protected function getUpdaterService(): UpdaterInterface
    {
        return GeneralUtility::makeInstance(ObjectManager::class)->get(UpdaterInterface::class);
    }
}

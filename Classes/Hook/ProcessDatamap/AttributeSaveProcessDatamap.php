<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Hook\ProcessDatamap;

use Pixelant\PxaProductManager\Domain\Repository\AttributeRepository;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

class AttributeSaveProcessDatamap
{
    /**
     * Validate the attribute type value.
     *
     * @param array $incomingFieldArray
     * @param string $table
     * @param string $id
     * @param DataHandler $dataHandler
     */
    // phpcs:ignore
    public function processDatamap_preProcessFieldArray(
        array $fieldArray,
        string $table,
        string $id,
        DataHandler $dataHandler
    ): void {
        if ($table === AttributeRepository::TABLE_NAME) {
            $clearSystemCache = false;

            // If Attribute is new, system cache needs to be cleared.
            if (!MathUtility::canBeInterpretedAsInteger($id)) {
                $clearSystemCache = true;
            }

            // If any new option was added, system cache needs to be cleared.
            if (isset($fieldArray['options']) && $clearSystemCache === false) {
                $options = GeneralUtility::trimExplode(',', $fieldArray['options'], true);
                foreach ($options as $option) {
                    if (!MathUtility::canBeInterpretedAsInteger($option)) {
                        $clearSystemCache = true;

                        break;
                    }
                }
            }

            if ($clearSystemCache === true) {
                $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
                $cacheManager->flushCachesInGroup('system');
            }
        }
    }
}

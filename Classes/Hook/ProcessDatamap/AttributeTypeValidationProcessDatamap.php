<?php

declare(strict_types=1);


namespace Pixelant\PxaProductManager\Hook\ProcessDatamap;


use Pixelant\PxaProductManager\Domain\Model\Attribute;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\MathUtility;

class AttributeTypeValidationProcessDatamap
{
    /**
     * Validate the attribute type value
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
    )
    {
        if ($table === 'tx_pxaproductmanager_domain_model_attribute') {
            if (isset($fieldArray['type']) && !in_array((int)$fieldArray['type'], Attribute::getAttributeTypes())) {
                $dataHandler->log(
                    $table,
                    $id,
                    2, // Update
                    0,
                    1, // Severity: error
                    'The attribute type "' . (int)$fieldArray['type'] . '" is not valid.',
                    0,
                    $data = [],
                    (int)$fieldArray['pid']
                );

                return;
            }

            if (!isset($fieldArray['type']) && !MathUtility::canBeInterpretedAsInteger($id)) {
                $dataHandler->log(
                    $table,
                    $id,
                    1, // New
                    0,
                    1, // Severity: error
                    'Attribute type is required.',
                    0,
                    $data = [],
                    (int)$fieldArray['pid'],
                    $id
                );
            }
        }
    }
}

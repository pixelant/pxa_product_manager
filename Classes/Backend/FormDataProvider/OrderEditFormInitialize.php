<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * Form data provider hook, add TCA on a fly
 *
 * @package Pixelant\PxaProductManager\Backend\FormDataProvider
 */
class OrderEditFormInitialize implements FormDataProviderInterface
{
    /**
     * @param array $result
     * @return array
     */
    public function addData(array $result): array
    {
        // Make sure it is only for order records
        if ($result['tableName'] !== 'tx_pxaproductmanager_domain_model_order') {
            return $result;
        }

        // Nothing to show if order is created manually ??
        if (StringUtility::beginsWith($result['databaseRow']['uid'], 'NEW')) {
            return $result;
        }

        $orderFields = unserialize($result['databaseRow']['serialized_order_fields']);

        if (empty($orderFields)) {
            return $result;
        }

        foreach ($orderFields as $fieldName => $fieldValue) {
            // Add TCA
            $result['processedTca']['columns'][$fieldName] = [
                'exclude' => 0,
                'label' => ucfirst(str_replace('_', ' ', $fieldName)),
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim',
                    'readOnly' => true
                ]
            ];

            // Add values
            $result['databaseRow'][$fieldName] = $fieldValue;
        }

        // Add field to types
        $result['processedTca']['types']['1']['showitem'] = str_replace(
            '|order_fields|',
            implode(',', array_keys($orderFields)),
            $result['processedTca']['types']['1']['showitem']
        );

        return $result;
    }
}

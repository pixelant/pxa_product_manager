<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormDataProvider;

use Pixelant\PxaProductManager\Domain\Model\Order;
use Pixelant\PxaProductManager\Utility\MainUtility;
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

        foreach ($orderFields as $fieldName => $fieldConfiguration) {
            // Add TCA
            switch ($fieldConfiguration['type']) {
                case Order::ORDERFIELD_TEXTAREA:
                    $result['processedTca']['columns'][$fieldName] = [
                        'label' => MainUtility::snakeCasePhraseToWords($fieldName),
                        'config' => [
                            'type' => 'text',
                            'readOnly' => true
                        ]
                    ];
                    break;
                default:
                    $result['processedTca']['columns'][$fieldName] = [
                        'label' => MainUtility::snakeCasePhraseToWords($fieldName),
                        'config' => [
                            'type' => 'none'
                        ]
                    ];
            }

            // Add values
            $result['databaseRow'][$fieldName] = $fieldConfiguration['value'];
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

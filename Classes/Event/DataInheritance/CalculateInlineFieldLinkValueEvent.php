<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\DataInheritance;

use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Calculates a value for inline fields of type tx_pxaproductmanager_domain_model_link.
 */
class CalculateInlineFieldLinkValueEvent
{
    /**
     * @param CalculatePropertyValueEvent $event
     * @return void
     */
    public function __invoke(CalculateInlineFieldValueEvent $event): void
    {
        if ($event->getTable() === 'tx_pxaproductmanager_domain_model_link') {
            // Use the fields name, link and description.
            $record = BackendUtility::getRecord(
                $event->getTable(),
                $event->getId(),
                'name, link, description'
            ) ?? [];

            if (!empty($record)) {
                // Combine name, link and description as the value.
                $event->setValue(implode(',', array_values($record)));
                $event->markInlineFieldValueIsSet();
            }
        }
    }
}

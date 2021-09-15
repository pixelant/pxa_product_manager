<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\DataInheritance;

use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Calculates a value for inline fields of type sys_file_reference.
 */
class CalculateInlineFieldFileReferenceValueEvent
{
    /**
     * @param CalculatePropertyValueEvent $event
     * @return void
     */
    public function __invoke(CalculateInlineFieldValueEvent $event): void
    {
        if ($event->getTable() === 'sys_file_reference') {
            // Use the field uid_local, points to the actual sys_file.
            $value = BackendUtility::getRecord(
                $event->getTable(),
                $event->getId(),
                'uid_local'
            )['uid_local'] ?? '';

            if (!empty($value)) {
                $event->setValue((string)$value);
                $event->markInlineFieldValueIsSet();
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\DataInheritance;

use TYPO3\CMS\Backend\Utility\BackendUtility;

class CalculateInlineFieldFileRerenceValueEvent
{
    /**
     * @param CalculatePropertyValueEvent $event
     * @return void
     */
    public function __invoke(CalculateInlineFieldValueEvent $event): void
    {
        if ($event->getTable() === 'sys_file_reference') {
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

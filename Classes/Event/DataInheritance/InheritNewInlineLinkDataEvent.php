<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\DataInheritance;

use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;

/**
 * Event to use when creating new inline data for inline field of type tx_pxaproductmanager_domain_model_link.
 */
class InheritNewInlineLinkDataEvent
{
    /**
     * @param InheritNewInlineDataEvent $event
     * @return void
     */
    public function __invoke(InheritNewInlineDataEvent $event): void
    {
        if ($event->getTable() === 'tx_pxaproductmanager_domain_model_link') {
            $keepFields = ['name', 'pid', 'link', 'description', 'hidden'];
            $compiledParentRecord = DataInheritanceUtility::compileRecordData(
                $event->getTable(),
                (int)$event->getId(),
                false
            );

            if (!empty($compiledParentRecord)) {
                // Clear out fields not included in keepFields.
                foreach (array_keys($compiledParentRecord) as $field) {
                    if (!in_array($field, $keepFields, true)) {
                        unset($compiledParentRecord[$field]);
                    }
                }

                // We never wan't to keep the uid.
                if (isset($compiledParentRecord['uid'])) {
                    unset($compiledParentRecord['uid']);
                }
                $event->setData($compiledParentRecord);
            }
        }
    }
}

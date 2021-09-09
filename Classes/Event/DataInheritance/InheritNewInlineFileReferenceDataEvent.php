<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\DataInheritance;

use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;

class InheritNewInlineFileReferenceDataEvent
{
    /**
     * @param InheritNewInlineDataEvent $event
     * @return void
     */
    public function __invoke(InheritNewInlineDataEvent $event): void
    {
        if ($event->getTable() === 'sys_file_reference') {
            $keepFields = ['uid_local', 'pid', 'title', 'crop', 'alternative', 'description', 'hidden'];
            $compiledParentRecord = DataInheritanceUtility::compileRecordData(
                $event->getTable(),
                (int)$event->getId(),
                false
            );

            if (!empty($compiledParentRecord)) {
                foreach (array_keys($compiledParentRecord) as $field) {
                    if (!in_array($field, $keepFields, true)) {
                        unset($compiledParentRecord[$field]);
                    }
                }

                if (isset($compiledParentRecord['uid'])) {
                    unset($compiledParentRecord['uid']);
                }
                $event->setData($compiledParentRecord);
            }
        }
    }
}

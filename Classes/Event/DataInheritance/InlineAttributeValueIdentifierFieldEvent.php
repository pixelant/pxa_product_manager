<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\DataInheritance;

use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;

/**
 * Event to get the identifier field name for attribute values table.
 */
class InlineAttributeValueIdentifierFieldEvent
{
    /**
     * @param InlineIdentifierFieldEvent $event
     * @return void
     */
    public function __invoke(InlineIdentifierFieldEvent $event): void
    {
        if ($event->getTable() === AttributeValueRepository::TABLE_NAME) {
            $event->setField('attribute');
            $event->markInlineIdentifierFieldIsSet();
        }
    }
}

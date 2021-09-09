<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\DataInheritance;

use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;

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

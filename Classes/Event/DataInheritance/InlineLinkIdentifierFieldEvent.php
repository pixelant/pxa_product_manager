<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Event\DataInheritance;

/**
 * Event to get the identifier field name for table tx_pxaproductmanager_domain_model_link.
 */
class InlineLinkIdentifierFieldEvent
{
    /**
     * @param InlineIdentifierFieldEvent $event
     * @return void
     */
    public function __invoke(InlineIdentifierFieldEvent $event): void
    {
        if ($event->getTable() === 'tx_pxaproductmanager_domain_model_link') {
            $event->setField('link');
            $event->markInlineIdentifierFieldIsSet();
        }
    }
}

<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\AttributeValue;

/**
 * General mapper for string values.
 */
class GeneralMapper extends AbstractMapper
{
    /**
     * {@inheritdoc}
     */
    public function map(AttributeValue $attributeValue): void
    {
        if ($attributeValue) {
            $attributeValue->setStringValue($attributeValue->getValue());
        }
    }
}

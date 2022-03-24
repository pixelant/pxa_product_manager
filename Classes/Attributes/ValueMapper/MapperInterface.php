<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ValueMapper;

use Pixelant\PxaProductManager\Domain\Model\AttributeValue;

interface MapperInterface
{
    /**
     * Set attribute value.
     * It should fine corresponding attribute value entity,
     * read value and set in given attribute value property.
     *
     * @param AttributeValue $attributeValue
     */
    public function map(AttributeValue $attributeValue): void;
}

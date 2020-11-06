<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Domain\Model;

class AttributeFile extends AbstractFileReference
{
    /**
     * Attribute uid if belongs to attribute.
     *
     * @var int
     */
    protected int $attribute = 0;

    /**
     * @return int
     */
    public function getAttribute(): int
    {
        return $this->attribute;
    }

    /**
     * @param int $attribute
     * @return AttributeFile
     */
    public function setAttribute(int $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }
}

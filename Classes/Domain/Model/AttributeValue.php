<?php

namespace Pixelant\PxaProductManager\Domain\Model;

use Pixelant\PxaProductManager\Attributes\ValueMapper\MapperFactory;
use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/*
 *  Copyright notice
 *
 *  (c) 2014
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class AttributeValue extends AbstractEntity
{
    use CanCreateCollection;

    /**
     * @var string
     */
    protected string $value = '';

    /**
     * String value for current product.
     *
     * @var string
     */
    protected string $stringValue = '';

    /**
     * Array value for current product.
     *
     * @var array
     */
    protected array $arrayValue = [];

    /**
     * True if the value mapper has done its job.
     *
     * @var bool
     */
    protected bool $mapped = false;

    /**
     * @var \Pixelant\PxaProductManager\Domain\Model\Product|null
     */
    protected $product;

    /**
     * @var \Pixelant\PxaProductManager\Domain\Model\Attribute|null
     */
    protected $attribute;

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product|null $product
     * @return AttributeValue
     */
    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return AttributeValue
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Attribute|null
     */
    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    /**
     * @param Attribute|null $attribute
     * @return AttributeValue
     */
    public function setAttribute(?Attribute $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Returns the value depending of the attribute type.
     *
     * @return mixed
     */
    public function getRenderValue()
    {
        if (
            $this->getAttribute()->isFalType() ||
            $this->getAttribute()->isSelectBoxType()
        ) {
            return $this->getArrayValue();
        }

        return $this->getStringValue();
    }

    /**
     * Returns the string value.
     *
     * @return string
     */
    public function getStringValue(): string
    {
        $this->map();

        return $this->stringValue;
    }

    /**
     * Sets the string value.
     *
     * @param string $value
     * @return Attribute
     */
    public function setStringValue($value)
    {
        $this->stringValue = $value;

        return $this;
    }

    /**
     * Returns the array value.
     *
     * @return array
     */
    public function getArrayValue(): array
    {
        $this->map();

        return $this->arrayValue;
    }

    /**
     * Returns text as array split by lines.
     * Only for attribute of type text.
     *
     * @return array
     */
    public function getTextToArray(): array
    {
        if ($this->getAttribute()->getType() === Attribute::ATTRIBUTE_TYPE_TEXT) {
            return GeneralUtility::trimExplode(LF, $this->getStringValue(), true);
        }

        return [];
    }

    /**
     * Sets the array value.
     *
     * @param array $value
     * @return Attribute
     */
    public function setArrayValue($value)
    {
        $this->arrayValue = $value;

        return $this;
    }

    /**
     * Returns if attribute have any none empty value.
     *
     * @return mixed
     */
    public function getHasNonEmptyValue()
    {
        $this->map();

        if ($this->getAttribute()->isFalType()) {
            return !empty($this->arrayValue);
        }

        if ($this->getAttribute()->isSelectBoxType()) {
            $options = $this->collection($this->arrayValue)
                ->filter(fn (Option $option) => $option->getValue() !== '')
                ->toArray();

            return count($options) > 0;
        }

        return strlen($this->stringValue) > 0;
    }

    /**
     * Returns true if the ValueMapper has done its job.
     *
     * @return bool
     */
    public function isMapped(): bool
    {
        return $this->mapped;
    }

    /**
     * Map values to the object.
     */
    public function map(): void
    {
        if ($this->isMapped()) {
            return;
        }

        GeneralUtility::makeInstance(MapperFactory::class)
            ->create($this)
            ->map($this->getProduct(), $this);
    }
}

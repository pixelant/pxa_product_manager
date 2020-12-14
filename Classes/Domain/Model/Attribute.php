<?php

namespace Pixelant\PxaProductManager\Domain\Model;

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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Attribute extends AbstractEntity
{
    /**
     * Attributes types.
     */
    public const ATTRIBUTE_TYPE_INPUT = 1;
    public const ATTRIBUTE_TYPE_TEXT = 2;
    public const ATTRIBUTE_TYPE_DATETIME = 3;
    public const ATTRIBUTE_TYPE_DROPDOWN = 4;
    public const ATTRIBUTE_TYPE_CHECKBOX = 5;
    public const ATTRIBUTE_TYPE_LINK = 6;
    public const ATTRIBUTE_TYPE_IMAGE = 7;
    public const ATTRIBUTE_TYPE_LABEL = 8;
    public const ATTRIBUTE_TYPE_MULTISELECT = 9;
    public const ATTRIBUTE_TYPE_FILE = 10;

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var int
     */
    protected int $type = 0;

    /**
     * @var bool
     */
    protected bool $required = false;

    /**
     * @var bool
     */
    protected bool $showInAttributeListing = false;

    /**
     * @var bool
     */
    protected bool $showInCompare = false;

    /**
     * @var string
     */
    protected string $identifier = '';

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Option>
     */
    protected ObjectStorage $options;

    /**
     * Label for checked checkbox.
     *
     * @var string
     */
    protected string $labelChecked = '';

    /**
     * Label for un-checked checkbox.
     *
     * @var string
     */
    protected string $labelUnchecked = '';

    /**
     * Default value for TCA.
     *
     * @var string
     */
    protected string $defaultValue = '';

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
     * Attribute label.
     *
     * @var string
     */
    protected string $label = '';

    /**
     * __construct.
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Extbase container doesn't call constructor,
     * which leads to an error "Typed property must not be accessed before initialization" on debug.
     */
    public function initializeObject(): void
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties.
     *
     * @return void
     */
    protected function initStorageObjects(): void
    {
        /*
         * Do not modify this method!
         * It will be rewritten on each save in the extension builder
         * You may modify the constructor of this class instead
         */
        $this->options = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the attribute name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the attribute name.
     *
     * @param string $name
     * @return Attribute
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the attribute type.
     *
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Sets the attribute type.
     *
     * @param int $type
     * @return Attribute
     */
    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Returns if attribute is required.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Sets if attribute is required.
     *
     * @param bool $required
     * @return Attribute
     */
    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Returns if attribute should be included in attribute listings.
     *
     * @return bool
     */
    public function isShowInAttributeListing(): bool
    {
        return $this->showInAttributeListing;
    }

    /**
     * Sets if attribute should be included in attribute listings.
     *
     * @param bool $showInAttributeListing
     * @return Attribute
     */
    public function setShowInAttributeListing(bool $showInAttributeListing): self
    {
        $this->showInAttributeListing = $showInAttributeListing;

        return $this;
    }

    /**
     * Returns if attribute should be included in compare view.
     *
     * @return bool
     */
    public function isShowInCompare(): bool
    {
        return $this->showInCompare;
    }

    /**
     * Sets if attribute should be included in compare view.
     *
     * @param bool $showInCompare
     * @return Attribute
     */
    public function setShowInCompare(bool $showInCompare): self
    {
        $this->showInCompare = $showInCompare;

        return $this;
    }

    /**
     * Returns the attribute identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Sets the attribute identifier.
     *
     * @param string $identifier
     * @return Attribute
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Returns the attribute options.
     *
     * @return ObjectStorage
     */
    public function getOptions(): ObjectStorage
    {
        return $this->options;
    }

    /**
     * Sets the attribute options.
     *
     * @param ObjectStorage $options
     * @return Attribute
     */
    public function setOptions(ObjectStorage $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Returns the attribute label for checked.
     *
     * @return string
     */
    public function getLabelChecked(): string
    {
        return $this->labelChecked;
    }

    /**
     * Sets the attribute label for checked.
     *
     * @param string $labelChecked
     * @return Attribute
     */
    public function setLabelChecked(string $labelChecked): self
    {
        $this->labelChecked = $labelChecked;

        return $this;
    }

    /**
     * Returns the attribute label for unchecked.
     *
     * @return string
     */
    public function getLabelUnchecked(): string
    {
        return $this->labelUnchecked;
    }

    /**
     * Sets the attribute label for unchecked.
     *
     * @param string $labelUnchecked
     * @return Attribute
     */
    public function setLabelUnchecked(string $labelUnchecked): self
    {
        $this->labelUnchecked = $labelUnchecked;

        return $this;
    }

    /**
     * Returns the default valule.
     *
     * @return string
     */
    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    /**
     * Sets the default valule.
     *
     * @param string $defaultValue
     * @return Attribute
     */
    public function setDefaultValue(string $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Returns the valule depending of the attribute type.
     *
     * @return mixed
     */
    public function getValue()
    {
        if (
            $this->isFalType() ||
            $this->isSelectBoxType()
        ) {
            return $this->arrayValue;
        }

        return $this->stringValue;
    }

    /**
     * Returns the string value.
     *
     * @return string
     */
    public function getStringValue(): string
    {
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
        if ($this->type === self::ATTRIBUTE_TYPE_TEXT) {
            return explode(LF, $this->stringValue);
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
     * Returns the attribute label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Sets the attribute label.
     *
     * @param string $label
     * @return Attribute
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Check if attribute type is FAL file.
     *
     * @return bool
     */
    public function isFalType(): bool
    {
        return $this->type === self::ATTRIBUTE_TYPE_IMAGE || $this->type === self::ATTRIBUTE_TYPE_FILE;
    }

    /**
     * If simple input.
     *
     * @return bool
     */
    public function isInputType(): bool
    {
        return $this->type === self::ATTRIBUTE_TYPE_INPUT;
    }

    /**
     * Check if is text area.
     *
     * @return bool
     */
    public function isTextArea(): bool
    {
        return $this->type === self::ATTRIBUTE_TYPE_TEXT;
    }

    /**
     * Date type check.
     *
     * @return bool
     */
    public function isDateType(): bool
    {
        return $this->type === self::ATTRIBUTE_TYPE_DATETIME;
    }

    /**
     * Select box type.
     * @return bool
     */
    public function isSelectBoxType(): bool
    {
        return in_array(
            $this->type,
            [
                self::ATTRIBUTE_TYPE_MULTISELECT,
                self::ATTRIBUTE_TYPE_DROPDOWN,
            ],
            true
        );
    }

    /**
     * Multiple select box.
     *
     * @return bool
     */
    public function isMultipleSelectBox(): bool
    {
        return $this->type === self::ATTRIBUTE_TYPE_MULTISELECT;
    }

    /**
     * Checkbox type.
     *
     * @return bool
     */
    public function isCheckboxType(): bool
    {
        return $this->type === self::ATTRIBUTE_TYPE_CHECKBOX;
    }

    /**
     * Link type.
     *
     * @return bool
     */
    public function isLinkType(): bool
    {
        return $this->type === self::ATTRIBUTE_TYPE_LINK;
    }

    /**
     * Label type.
     *
     * @return bool
     */
    public function isLabelType(): bool
    {
        return $this->type === self::ATTRIBUTE_TYPE_LABEL;
    }

    /**
     * As string return value.
     *
     * @return string
     */
    public function __toString()
    {
        if (is_array($this->value)) {
            return implode(',', $this->value);
        }

        return (string)$this->value;
    }
}

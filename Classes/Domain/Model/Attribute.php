<?php

namespace Pixelant\PxaProductManager\Domain\Model;

/***************************************************************
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
 ***************************************************************/

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 *
 *
 * @package pxa_product_manager
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Attribute extends AbstractEntity
{
    /**
     * Attributes types
     */
    const ATTRIBUTE_TYPE_INPUT = 1;
    const ATTRIBUTE_TYPE_TEXT = 2;
    const ATTRIBUTE_TYPE_DATETIME = 3;
    const ATTRIBUTE_TYPE_DROPDOWN = 4;
    const ATTRIBUTE_TYPE_CHECKBOX = 5;
    const ATTRIBUTE_TYPE_LINK = 6;
    const ATTRIBUTE_TYPE_IMAGE = 7;
    const ATTRIBUTE_TYPE_LABEL = 8;
    const ATTRIBUTE_TYPE_MULTISELECT = 9;
    const ATTRIBUTE_TYPE_FILE = 10;

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var integer
     */
    protected int $type = 0;

    /**
     * @var boolean
     */
    protected bool $required = false;

    /**
     * @var boolean
     */
    protected bool $showInAttributeListing = false;

    /**
     * @var boolean
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
     * Label for checked checkbox
     *
     * @var string
     */
    protected string $labelChecked = '';

    /**
     * Label for un-checked checkbox
     *
     * @var string
     */
    protected string $labelUnchecked = '';

    /**
     * Default value for TCA
     *
     * @var string
     */
    protected string $defaultValue = '';

    /**
     * Value for current product
     *
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected string $label = '';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Extbase container doesn't call constructor,
     * which leads to an error "Typed property must not be accessed before initialization" on debug
     */
    public function initializeObject()
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties.
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        /**
         * Do not modify this method!
         * It will be rewritten on each save in the extension builder
         * You may modify the constructor of this class instead
         */
        $this->options = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Attribute
     */
    public function setName(string $name): Attribute
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Attribute
     */
    public function setType(int $type): Attribute
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     * @return Attribute
     */
    public function setRequired(bool $required): Attribute
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowInAttributeListing(): bool
    {
        return $this->showInAttributeListing;
    }

    /**
     * @param bool $showInAttributeListing
     * @return Attribute
     */
    public function setShowInAttributeListing(bool $showInAttributeListing): Attribute
    {
        $this->showInAttributeListing = $showInAttributeListing;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowInCompare(): bool
    {
        return $this->showInCompare;
    }

    /**
     * @param bool $showInCompare
     * @return Attribute
     */
    public function setShowInCompare(bool $showInCompare): Attribute
    {
        $this->showInCompare = $showInCompare;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     * @return Attribute
     */
    public function setIdentifier(string $identifier): Attribute
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getOptions(): ObjectStorage
    {
        return $this->options;
    }

    /**
     * @param ObjectStorage $options
     * @return Attribute
     */
    public function setOptions(ObjectStorage $options): Attribute
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabelChecked(): string
    {
        return $this->labelChecked;
    }

    /**
     * @param string $labelChecked
     * @return Attribute
     */
    public function setLabelChecked(string $labelChecked): Attribute
    {
        $this->labelChecked = $labelChecked;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabelUnchecked(): string
    {
        return $this->labelUnchecked;
    }

    /**
     * @param string $labelUnchecked
     * @return Attribute
     */
    public function setLabelUnchecked(string $labelUnchecked): Attribute
    {
        $this->labelUnchecked = $labelUnchecked;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    /**
     * @param string $defaultValue
     * @return Attribute
     */
    public function setDefaultValue(string $defaultValue): Attribute
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Attribute
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return Attribute
     */
    public function setLabel(string $label): Attribute
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Check if attribute type is FAL file
     *
     * @return bool
     */
    public function isFalType(): bool
    {
        return $this->type === self::ATTRIBUTE_TYPE_IMAGE || $this->type === self::ATTRIBUTE_TYPE_FILE;
    }
}

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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 *
 *
 * @package pxa_product_manager
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Attribute extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     *
     *
     */
    const TCA_ATTRIBUTE_PREFIX = 'tx_pxaproductmanager_attribute_';

    /**
     * Additional prefix for images
     */
    const TCA_ATTRIBUTE_IMAGE_PREFIX = 'ATTRIBUTE_IMG_';

    /**
     *
     *
     */
    const ATTRIBUTE_TYPE_INPUT = 1;

    /**
     *
     *
     */
    const ATTRIBUTE_TYPE_TEXT = 2;

    /**
     *
     *
     */
    const ATTRIBUTE_TYPE_DATETIME = 3;

    /**
     *
     *
     */
    const ATTRIBUTE_TYPE_DROPDOWN = 4;

    /**
     *
     *
     */
    const ATTRIBUTE_TYPE_CHECKBOX = 5;

    /**
     *
     *
     */
    const ATTRIBUTE_TYPE_LINK = 6;

    /**
     *
     *
     */
    const ATTRIBUTE_TYPE_IMAGE = 7;

    /**
     *
     *
     */
    const ATTRIBUTE_TYPE_LABEL = 8;

    const ATTRIBUTE_TYPE_MULTISELECT = 9;


    /**
     * name
     *
     * @var \string
     * @validate NotEmpty
     */
    protected $name = '';

    /**
     * type
     *
     * @var \integer
     * @validate NotEmpty
     */
    protected $type = 0;

    /**
     * required
     *
     * @var boolean
     */
    protected $required = false;

    /**
     * showInAttributeListing
     *
     * @var boolean
     */
    protected $showInAttributeListing = false;

    /**
     * showInCompare
     *
     * @var boolean
     */
    protected $showInCompare = false;

    /**
     * identifier
     *
     * @var \string
     */
    protected $identifier = '';

    /**
     * options
     *
     * @lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Option>
     */
    protected $options;

    /**
     * Label for checked checkbox
     *
     * @var string
     */
    protected $labelChecked = '';

    /**
     * Label for un-checked checkbox
     *
     * @var string
     */
    protected $labelUnchecked = '';

    /**
     * Default value for TCA
     *
     * @var string
     */
    protected $defaultValue = '';

    /**
     * Value for current product
     *
     * @var string|array
     */
    protected $value;

    /**
     * label
     *
     * @var \string
     */
    protected $label = '';

    /**
     * Icon
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @lazy
     */
    protected $icon;

    /**
     * Returns the name
     *
     * @return \string $name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param \string $name
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the type
     *
     * @return \integer $type
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Sets the type
     *
     * @param \integer $type
     * @return void
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * Returns the required
     *
     * @return boolean $required
     */
    public function getRequired(): bool
    {
        return $this->required;
    }

    /**
     * Sets the required
     *
     * @param boolean $required
     * @return void
     */
    public function setRequired(bool $required)
    {
        $this->required = $required;
    }

    /**
     * Returns the boolean state of required
     *
     * @return boolean
     */
    public function isRequired(): bool
    {
        return $this->getRequired();
    }

    /**
     * Returns the showInAttributeListing
     *
     * @return boolean $showInAttributeListing
     */
    public function getShowInAttributeListing(): bool
    {
        return $this->showInAttributeListing;
    }

    /**
     * Sets the showInAttributeListing
     *
     * @param boolean $showInAttributeListing
     * @return void
     */
    public function setShowInAttributeListing(bool $showInAttributeListing)
    {
        $this->showInAttributeListing = $showInAttributeListing;
    }

    /**
     * Returns the boolean state of showInAttributeListing
     *
     * @return boolean
     */
    public function isShowInAttributeListing(): bool
    {
        return $this->getShowInAttributeListing();
    }

    /**
     * Returns the identifier
     *
     * @param bool $strict
     * @return \string $identifier
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Sets the identifier
     *
     * @param \string $identifier
     * @return void
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

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
     * Adds a Option
     *
     * @param Option $option
     * @return void
     */
    public function addOption(Option $option)
    {
        $this->options->attach($option);
    }

    /**
     * Removes a Option
     *
     * @param Option $optionToRemove The Option to be removed
     * @return void
     */
    public function removeOption(Option $optionToRemove)
    {
        $this->options->detach($optionToRemove);
    }

    /**
     * Returns the options
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Option> $options
     */
    public function getOptions(): ObjectStorage
    {
        return $this->options;
    }

    /**
     * Sets the options
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Option> $options
     * @return void
     */
    public function setOptions(ObjectStorage $options)
    {
        $this->options = $options;
    }

    /**
     * Returns the showInCompare
     *
     * @return boolean $showInCompare
     */
    public function getShowInCompare(): bool
    {
        return $this->showInCompare;
    }

    /**
     * Sets the showInCompare
     *
     * @param boolean $showInCompare
     * @return void
     */
    public function setShowInCompare(bool $showInCompare)
    {
        $this->showInCompare = $showInCompare;
    }

    /**
     * Returns the boolean state of showInCompare
     *
     * @return boolean
     */
    public function isShowInCompare(): bool
    {
        return $this->getShowInCompare();
    }


    /**
     * Returns the defaultValue
     *
     * @return \string $defaultValue
     */
    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    /**
     * Sets the defaultValue
     *
     * @param \string $defaultValue
     * @return void
     */
    public function setDefaultValue(string $defaultValue)
    {
        $this->defaultValue = $defaultValue;
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
     */
    public function setLabelChecked(string $labelChecked)
    {
        $this->labelChecked = $labelChecked;
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
     */
    public function setLabelUnchecked(string $labelUnchecked)
    {
        $this->labelUnchecked = $labelUnchecked;
    }

    /**
     * @return string|array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the label
     *
     * @return \string $label
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Sets the label
     *
     * @param \string $label
     * @return void
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    /**
     * Sets the icon value
     *
     * @api
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $icon
     */
    public function setIcon(\TYPO3\CMS\Extbase\Domain\Model\FileReference $icon)
    {
        $this->icon = $icon;
    }

    /**
     * Gets the icon value
     *
     * @api
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getIcon()
    {
        return $this->icon;
    }
}

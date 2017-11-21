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

/**
 *
 *
 * @package pxa_product_manager
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class AttributeSet extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     *
     * @var \string
     * @validate NotEmpty
     */
    protected $name;

    /**
     * attributes
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Attribute>
     */
    protected $attributes;

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
        $this->attributes = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Adds a Attribute
     *
     * @param \Pixelant\PxaProductManager\Domain\Model\Attribute $attribute
     * @return void
     */
    public function addAttribute(\Pixelant\PxaProductManager\Domain\Model\Attribute $attribute)
    {
        $this->attributes->attach($attribute);
    }

    /**
     * Removes a Attribute
     *
     * @param \Pixelant\PxaProductManager\Domain\Model\Attribute $attributeToRemove The Attribute to be removed
     * @return void
     */
    public function removeAttribute(\Pixelant\PxaProductManager\Domain\Model\Attribute $attributeToRemove)
    {
        $this->attributes->detach($attributeToRemove);
    }

    // @codingStandardsIgnoreStart
    /**
     * Returns the attributes
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Attribute> $attributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets the attributes
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Attribute> $attributes
     * @return void
     */
    public function setAttributes(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $attributes)
    {
        $this->attributes = $attributes;
    }
    // @codingStandardsIgnoreEnd

    /**
     * Returns the name
     *
     * @return \string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param \string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}

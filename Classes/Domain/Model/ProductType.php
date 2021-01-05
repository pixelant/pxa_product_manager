<?php

namespace Pixelant\PxaProductManager\Domain\Model;

/*
 *
 *  Copyright notice
 *
 *  (c) 2017
 *
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
 * Filter.
 */
class ProductType extends AbstractEntity
{
    /**
     * @var string
     */
    protected string $name = '';

    /**
     * Attribute sets.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\AttributeSet>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ObjectStorage $attributeSets;

    /**
     * Fields to be inherited from parent to child products.
     *
     * @var array
     */
    protected array $inheritFields = [];

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
        $this->attributeSets = new ObjectStorage();
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
     * @return ProductType
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getAttributeSets(): ObjectStorage
    {
        return $this->attributeSets;
    }

    /**
     * Add attribute set.
     *
     * @param AttributeSet $attributeSet
     * @return ProductType
     */
    public function addAttributeSet(AttributeSet $attributeSet): self
    {
        $this->attributeSets->attach($attributeSet);

        return $this;
    }

    /**
     * @param ObjectStorage $attributeSets
     * @return ProductType
     */
    public function setAttributeSets(ObjectStorage $attributeSets): self
    {
        $this->attributeSets = $attributeSets;

        return $this;
    }

    /**
     * @return array
     */
    public function getInheritFields(): array
    {
        return $this->inheritFields;
    }

    /**
     * @param array $inheritFields
     */
    public function setInheritFields(array $inheritFields)
    {
        $this->inheritFields = $inheritFields;
    }
}

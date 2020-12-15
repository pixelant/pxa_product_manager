<?php

namespace Pixelant\PxaProductManager\Domain\Model;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
class AttributeSet extends AbstractEntity
{
    use CanCreateCollection;

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Attribute>
     */
    protected ObjectStorage $attributes;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Category>
     */
    protected ObjectStorage $categories;

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
        $this->attributes = new ObjectStorage();
        $this->categories = new ObjectStorage();
    }

    /**
     * Adds a Attribute.
     *
     * @param Attribute $attribute
     * @return void
     */
    public function addAttribute(Attribute $attribute): void
    {
        $this->attributes->attach($attribute);
    }

    /**
     * Removes a Attribute.
     *
     * @param Attribute $attributeToRemove The Attribute to be removed
     * @return void
     */
    public function removeAttribute(Attribute $attributeToRemove): void
    {
        $this->attributes->detach($attributeToRemove);
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
     * @return AttributeSet
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getAttributes(): ObjectStorage
    {
        return $this->attributes;
    }

    /**
     * Return attributes included in listings
     *
     * @return array
     */
    public function getListingAttributes(): array
    {
        return $this->collection($this->getAttributes())
            ->filter(fn (Attribute $attribute) => $attribute->isShowInAttributeListing())
            ->toArray();
    }

    /**
     * @param ObjectStorage $attributes
     * @return AttributeSet
     */
    public function setAttributes(ObjectStorage $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getCategories(): ObjectStorage
    {
        return $this->categories;
    }

    /**
     * @param ObjectStorage $categories
     * @return AttributeSet
     */
    public function setCategories(ObjectStorage $categories): self
    {
        $this->categories = $categories;

        return $this;
    }
}

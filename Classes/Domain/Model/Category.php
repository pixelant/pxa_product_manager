<?php

namespace Pixelant\PxaProductManager\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\Category as CategoryExtbase;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
class Category extends CategoryExtbase
{

    /**
     * parent
     *
     * @var \Pixelant\PxaProductManager\Domain\Model\Category|NULL
     * @lazy
     */
    protected $parent;

    /**
     * @var \string
     */
    protected $alternativeTitle = '';

    /**
     * @var \string
     */
    protected $pathSegment = '';

    /**
     * @var \string
     */
    protected $keywords = '';

    /**
     * @var \string
     */
    protected $metaDescription = '';

    /**
     * Image
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @lazy
     */
    protected $image;

    /**
     * @var boolean
     */
    protected $hidden;

    /**
     * @var boolean
     */
    protected $deleted;

    /**
     * Attribute sets
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\AttributeSet>
     */
    protected $attributeSets;

    /**
     * Banner Image
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @lazy
     */
    protected $bannerImage;

    /**
     * taxRate
     *
     * @var float $taxRate
     */
    protected $taxRate = 0.00;

    /**
     * @var \string
     */
    protected $cardViewTemplate = '';

    /**
     * @var \string
     */
    protected $singleViewTemplate = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Category>
     * @lazy
     */
    protected $subCategories;

    /**
     * @var string
     */
    protected $slug = '';

    /**
     * __construct
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
        $this->attributeSets = new ObjectStorage();
        $this->subCategories = new ObjectStorage();
    }

    /**
     * getImage alias
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * setImage alias
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     * @return void
     */
    public function setImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image)
    {
        $this->image = $image;
    }

    /**
     * Get alternative title
     *
     * @return \string
     */
    public function getAlternativeTitle(): string
    {
        return $this->alternativeTitle;
    }

    /**
     * Set alternative title
     *
     * @param \string $alternativeTitle
     * @return void
     */
    public function setAlternativeTitle(string $alternativeTitle)
    {
        $this->alternativeTitle = $alternativeTitle;
    }

    /**
     * Get path segment
     *
     * @return \string
     */
    public function getPathSegment(): string
    {
        return $this->pathSegment;
    }

    /**
     * Set path segment
     *
     * @param \string $pathSegment
     * @return void
     */
    public function setPathSegment(string $pathSegment)
    {
        $this->pathSegment = $pathSegment;
    }

    /**
     * Get keywords
     *
     * @return \string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * Set keywords
     *
     * @param \string $keywords keywords
     * @return void
     */
    public function setKeywords(string $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription metaDescription
     * @return void
     */
    public function setMetaDescription(string $metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * Get Hidden
     *
     * @return boolean
     */
    public function getHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Set Hidden
     *
     * @param boolean $hidden
     * @return void
     */
    public function setHidden(bool $hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * Get Deleted
     *
     * @return boolean
     */
    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * Set Deleted
     *
     * @param boolean $deleted
     * @return void
     */
    public function setDeleted(bool $deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * Returns the Attributes
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\AttributeSet>
     */
    public function getAttributeSets(): ObjectStorage
    {
        return $this->attributeSets;
    }

    // @codingStandardsIgnoreStart
    /**
     * Sets the Attributes
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\AttributeSet> $attributeSets
     * @return void
     */
    // @codingStandardsIgnoreEnd
    public function setAttributeSets(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $attributeSets)
    {
        $this->attributeSets = $attributeSets;
    }

    /**
     * Adds a pxapmAttribute
     *
     * @param \Pixelant\PxaProductManager\Domain\Model\AttributeSet $attributeSet
     * @return void
     */
    public function addAttributeSet(\Pixelant\PxaProductManager\Domain\Model\AttributeSet $attributeSet)
    {
        $this->attributeSets->attach($attributeSet);
    }

    /**
     * Removes a AttributeSet
     *
     * @param \Pixelant\PxaProductManager\Domain\Model\AttributeSet $attributeSet
     * @return void
     */
    public function removeAttributeSet(\Pixelant\PxaProductManager\Domain\Model\AttributeSet $attributeSet)
    {
        $this->attributeSets->detach($attributeSet);
    }

    /**
     * getBannerImage
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getBannerImage()
    {
        return $this->bannerImage;
    }

    /**
     * setBannerImage
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $bannerImage
     * @return void
     */
    public function setBannerImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $bannerImage)
    {
        $this->bannerImage = $bannerImage;
    }

    /**
     * Returns the pxaPmTaxRate
     *
     * @return float $pxaPmTaxRate
     */
    public function getTaxRate() : float
    {
        return $this->taxRate;
    }

    /**
     * Sets the taxRate
     *
     * @param float $taxRate
     * @return void
     */
    public function setTaxRate(float $taxRate)
    {
        $this->taxRate = $taxRate;
    }

    /**
     * Get CardViewTemplate
     *
     * @return \string
     */
    public function getCardViewTemplate(): string
    {
        return $this->cardViewTemplate;
    }

    /**
     * Set CardViewTemplate
     *
     * @param \string $cardViewTemplate CardViewTemplate
     * @return void
     */
    public function setCardViewTemplate(string $cardViewTemplate)
    {
        $this->cardViewTemplate = $cardViewTemplate;
    }

    /**
     * Get SingleViewTemplate
     *
     * @return \string
     */
    public function getSingleViewTemplate(): string
    {
        return $this->singleViewTemplate;
    }

    /**
     * Set SingleViewTemplate
     *
     * @param \string $singleViewTemplate SingleViewTemplate
     * @return void
     */
    public function setSingleViewTemplate(string $singleViewTemplate)
    {
        $this->singleViewTemplate = $singleViewTemplate;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Category>
     */
    public function getSubCategories()
    {
        return $this->subCategories;
    }

    /**
     * @param ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Category> $subCategories
     */
    public function setSubCategories(ObjectStorage $subCategories)
    {
        $this->subCategories = $subCategories;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }
}

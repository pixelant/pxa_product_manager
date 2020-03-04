<?php

namespace Pixelant\PxaProductManager\Domain\Model;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use TYPO3\CMS\Extbase\Domain\Model\Category as CategoryExtbase;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
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
    use AbleCacheProperties, CanCreateCollection;

    /**
     * @var \Pixelant\PxaProductManager\Domain\Model\Category
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $parent = null;

    /**
     * @var string
     */
    protected string $alternativeTitle = '';

    /**
     * @var string
     */
    protected string $keywords = '';

    /**
     * @var string
     */
    protected string $metaDescription = '';

    /**
     * Image
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ?FileReference $image = null;

    /**
     * @var boolean
     */
    protected bool $hidden = false;

    /**
     * @var boolean
     */
    protected bool $deleted = false;

    /**
     * Attribute sets
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\AttributeSet>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ObjectStorage $attributesSets;

    /**
     * Banner Image
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ?FileReference $bannerImage = null;

    /**
     * @var float $taxRate
     */
    protected float $taxRate = 0.00;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Category>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ObjectStorage $subCategories;

    /**
     * @var int
     */
    protected int $contentPage = 0;

    /**
     * @var int
     */
    protected int $contentColPos = 0;

    /**
     * @var bool
     */
    protected bool $hiddenInNavigation = false;

    /**
     * @var bool
     */
    protected bool $hideProducts = false;

    /**
     * __construct
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
        $this->attributesSets = new ObjectStorage();
        $this->subCategories = new ObjectStorage();
    }

    /**
     * @return string
     */
    public function getAlternativeTitle(): string
    {
        return $this->alternativeTitle;
    }

    /**
     * @param string $alternativeTitle
     * @return Category
     */
    public function setAlternativeTitle(string $alternativeTitle): Category
    {
        $this->alternativeTitle = $alternativeTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     * @return Category
     */
    public function setKeywords(string $keywords): Category
    {
        $this->keywords = $keywords;
        return $this;
    }

    /**
     * @return string
     */
    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     * @return Category
     */
    public function setMetaDescription(string $metaDescription): Category
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    /**
     * @return FileReference|null
     */
    public function getImage(): ?FileReference
    {
        return $this->image;
    }

    /**
     * @param FileReference|null $image
     * @return Category
     */
    public function setImage(?FileReference $image): Category
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     * @return Category
     */
    public function setHidden(bool $hidden): Category
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     * @return Category
     */
    public function setDeleted(bool $deleted): Category
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getAttributesSets(): ObjectStorage
    {
        return $this->attributesSets;
    }

    /**
     * Add attribute set
     *
     * @param AttributeSet $attributeSet
     * @return Category
     */
    public function addAttributeSet(AttributeSet $attributeSet): Category
    {
        $this->attributesSets->attach($attributeSet);
        return $this;
    }

    /**
     * @param ObjectStorage $attributesSets
     * @return Category
     */
    public function setAttributesSets(ObjectStorage $attributesSets): Category
    {
        $this->attributesSets = $attributesSets;
        return $this;
    }

    /**
     * @return FileReference|null
     */
    public function getBannerImage(): ?FileReference
    {
        return $this->bannerImage;
    }

    /**
     * @param FileReference|null $bannerImage
     * @return Category
     */
    public function setBannerImage(?FileReference $bannerImage): Category
    {
        $this->bannerImage = $bannerImage;
        return $this;
    }

    /**
     * @return float
     */
    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    /**
     * @param float $taxRate
     * @return Category
     */
    public function setTaxRate(float $taxRate): Category
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getSubCategories(): ObjectStorage
    {
        return $this->subCategories;
    }

    /**
     * @param ObjectStorage $subCategories
     * @return Category
     */
    public function setSubCategories(ObjectStorage $subCategories): Category
    {
        $this->subCategories = $subCategories;
        return $this;
    }

    /**
     * @return int
     */
    public function getContentPage(): int
    {
        return $this->contentPage;
    }

    /**
     * @param int $contentPage
     * @return Category
     */
    public function setContentPage(int $contentPage): Category
    {
        $this->contentPage = $contentPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getContentColPos(): int
    {
        return $this->contentColPos;
    }

    /**
     * @param int $contentColPos
     * @return Category
     */
    public function setContentColPos(int $contentColPos): Category
    {
        $this->contentColPos = $contentColPos;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHiddenInNavigation(): bool
    {
        return $this->hiddenInNavigation;
    }

    /**
     * @param bool $hiddenInNavigation
     * @return Category
     */
    public function setHiddenInNavigation(bool $hiddenInNavigation): Category
    {
        $this->hiddenInNavigation = $hiddenInNavigation;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHideProducts(): bool
    {
        return $this->hideProducts;
    }

    /**
     * @param bool $hideProducts
     * @return Category
     */
    public function setHideProducts(bool $hideProducts): Category
    {
        $this->hideProducts = $hideProducts;
        return $this;
    }

    /**
     * Return parents root line up till to root category
     * From bottom to up. Current first
     *
     * @return Category[]
     */
    public function getParentsRootLine(): array
    {
        return $this->getCachedProperty('parentsRootLine', function () {
            $rootLine = [];
            $category = $this;

            do {
                $rootLine[] = $category;
                $category = $category->getParent();
            } while ($category !== null && !in_array($category, $rootLine, true));

            return $rootLine;
        });
    }

    /**
     * Return parents root line up till to root category
     * Root category first, current last
     *
     * @return Category[]
     */
    public function getParentsRootLineReverse(): array
    {
        return array_reverse($this->getParentsRootLine());
    }

    /**
     * Get root line from root category down to current, exclude hidden in navigation
     *
     * @return array
     */
    public function getNavigationRootLine(): array
    {
        return $this
            ->collection($this->getParentsRootLineReverse())
            ->filter(fn(Category $category) => !$category->isHiddenInNavigation())
            ->toArray();
    }
}

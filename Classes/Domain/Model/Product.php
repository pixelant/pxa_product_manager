<?php
declare(strict_types=1);

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

use DateTime;
use Pixelant\PxaProductManager\Domain\Collection\Collection;
use TYPO3\CMS\Core\Utility\GeneralUtility as GU;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Product model
 */
class Product extends AbstractEntity
{
    use AbleCacheProperties;

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @var string
     */
    protected string $sku = '';

    /**
     * @var float
     */
    protected float $price = 0.0;

    /**
     * @var float
     */
    protected float $taxRate = 0.0;

    /**
     * @var string
     */
    protected string $teaser = '';

    /**
     * @var string
     */
    protected string $description = '';

    /**
     * @var string
     */
    protected string $usp = '';

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
     * @var bool
     */
    protected bool $deleted = false;

    /**
     * @var DateTime
     */
    protected ?DateTime $crdate = null;

    /**
     * @var DateTime
     */
    protected ?DateTime $tstamp = null;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Category>
     */
    protected ObjectStorage $categories;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Product>
     */
    protected ObjectStorage $relatedProducts;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Product>
     */
    protected ObjectStorage $subProducts;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Product>
     */
    protected ObjectStorage $accessories;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Image>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected ObjectStorage $images;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\AttributeFile>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected ObjectStorage $attributeFiles;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Link>
     */
    protected ObjectStorage $links;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected ObjectStorage $falLinks;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected ObjectStorage $assets;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\AttributeValue>
     */
    protected ObjectStorage $attributeValues;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\AttributeSet>
     */
    protected ObjectStorage $attributesSets;

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
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->categories = new ObjectStorage();
        $this->relatedProducts = new ObjectStorage();
        $this->subProducts = new ObjectStorage();
        $this->accessories = new ObjectStorage();
        $this->images = new ObjectStorage();
        $this->attributeFiles = new ObjectStorage();
        $this->links = new ObjectStorage();
        $this->falLinks = new ObjectStorage();
        $this->assets = new ObjectStorage();
        $this->attributeValues = new ObjectStorage();
        $this->attributesSets = new ObjectStorage();
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
     * @return Product
     */
    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     * @return Product
     */
    public function setSku(string $sku): Product
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return Product
     */
    public function setPrice(float $price): Product
    {
        $this->price = $price;
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
     * @return Product
     */
    public function setTaxRate(float $taxRate): Product
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    /**
     * @return string
     */
    public function getTeaser(): string
    {
        return $this->teaser;
    }

    /**
     * @param string $teaser
     * @return Product
     */
    public function setTeaser(string $teaser): Product
    {
        $this->teaser = $teaser;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Product
     */
    public function setDescription(string $description): Product
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsp(): string
    {
        return $this->usp;
    }

    /**
     * @param string $usp
     * @return Product
     */
    public function setUsp(string $usp): Product
    {
        $this->usp = $usp;
        return $this;
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
     * @return Product
     */
    public function setAlternativeTitle(string $alternativeTitle): Product
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
     * @return Product
     */
    public function setKeywords(string $keywords): Product
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
     * @return Product
     */
    public function setMetaDescription(string $metaDescription): Product
    {
        $this->metaDescription = $metaDescription;
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
     * @return Product
     */
    public function setDeleted(bool $deleted): Product
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCrdate(): DateTime
    {
        return $this->crdate;
    }

    /**
     * @param DateTime $crdate
     * @return Product
     */
    public function setCrdate(DateTime $crdate): Product
    {
        $this->crdate = $crdate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTstamp(): DateTime
    {
        return $this->tstamp;
    }

    /**
     * @param DateTime $tstamp
     * @return Product
     */
    public function setTstamp(DateTime $tstamp): Product
    {
        $this->tstamp = $tstamp;
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
     * @return Product
     */
    public function setCategories(ObjectStorage $categories): Product
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getRelatedProducts(): ObjectStorage
    {
        return $this->relatedProducts;
    }

    /**
     * @param ObjectStorage $relatedProducts
     * @return Product
     */
    public function setRelatedProducts(ObjectStorage $relatedProducts): Product
    {
        $this->relatedProducts = $relatedProducts;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getSubProducts(): ObjectStorage
    {
        return $this->subProducts;
    }

    /**
     * @param ObjectStorage $subProducts
     * @return Product
     */
    public function setSubProducts(ObjectStorage $subProducts): Product
    {
        $this->subProducts = $subProducts;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getAccessories(): ObjectStorage
    {
        return $this->accessories;
    }

    /**
     * @param ObjectStorage $accessories
     * @return Product
     */
    public function setAccessories(ObjectStorage $accessories): Product
    {
        $this->accessories = $accessories;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getImages(): ObjectStorage
    {
        return $this->images;
    }

    /**
     * @param ObjectStorage $images
     * @return Product
     */
    public function setImages(ObjectStorage $images): Product
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getAttributeFiles(): ObjectStorage
    {
        return $this->attributeFiles;
    }

    /**
     * @param ObjectStorage $attributeFiles
     * @return Product
     */
    public function setAttributeFiles(ObjectStorage $attributeFiles): Product
    {
        $this->attributeFiles = $attributeFiles;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getLinks(): ObjectStorage
    {
        return $this->links;
    }

    /**
     * @param ObjectStorage $links
     * @return Product
     */
    public function setLinks(ObjectStorage $links): Product
    {
        $this->links = $links;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getFalLinks(): ObjectStorage
    {
        return $this->falLinks;
    }

    /**
     * @param ObjectStorage $falLinks
     * @return Product
     */
    public function setFalLinks(ObjectStorage $falLinks): Product
    {
        $this->falLinks = $falLinks;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getAssets(): ObjectStorage
    {
        return $this->assets;
    }

    /**
     * @param ObjectStorage $assets
     * @return Product
     */
    public function setAssets(ObjectStorage $assets): Product
    {
        $this->assets = $assets;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getAttributeValues(): ObjectStorage
    {
        return $this->attributeValues;
    }

    /**
     * @param ObjectStorage $attributeValues
     * @return Product
     */
    public function setAttributeValues(ObjectStorage $attributeValues): Product
    {
        $this->attributeValues = $attributeValues;
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
     * @param AttributeSet $attributeSet
     * @return Product
     */
    public function addAttributesSet(AttributeSet $attributeSet): Product
    {
        $this->attributesSets->attach($attributeSet);
        return $this;
    }

    /**
     * @param ObjectStorage $attributesSets
     * @return Product
     */
    public function setAttributesSets(ObjectStorage $attributesSets): Product
    {
        $this->attributesSets = $attributesSets;
        return $this;
    }

    /**
     * Return all attribute set.
     * It fetch every attribute set of every category from parents tree
     * + product own attributes sets
     *
     * @return array
     */
    public function getAllAttributesSets(): array
    {
        return $this->getCachedProperty(__METHOD__, function () {
            $attributesSets = $this->collection($this->attributesSets);

            $categoriesAttributeSets = $this->collection($this->getCategoriesWithParents())
                ->pluck('attributesSets')
                ->shiftLevel();

            return array_values(
                $attributesSets->unionUniqueProperty($categoriesAttributeSets, 'uid')->toArray()
            );
        });
    }

    /**
     * Get all products categories including parents
     *
     * @return array
     */
    public function getCategoriesWithParents(): array
    {
        // Fetch all parents and merge
        $all = array_merge(...array_map(
            fn(Category $category) => $category->getParentsRootLine(),
            $this->categories->toArray()
        ));

        return $this->collection($all)->unique()->toArray();
    }

    /**
     * Shortcut for collection instance
     *
     * @param $items
     * @return Collection
     */
    protected function collection($items): Collection
    {
        return GU::makeInstance(Collection::class, $items);
    }
}

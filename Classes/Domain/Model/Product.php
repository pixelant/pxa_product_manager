<?php

declare(strict_types=1);

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

use DateTime;
use Pixelant\PxaProductManager\Attributes\ValueMapper\MapperServiceInterface;
use Pixelant\PxaProductManager\Attributes\ValueUpdater\UpdaterInterface;
use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Formatter\PriceFormatter;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Product model.
 */
class Product extends AbstractEntity
{
    use CanCacheProperties;
    use CanCreateCollection;

    /**
     * @var MapperServiceInterface
     */
    protected MapperServiceInterface $attributesValuesMapper;

    /**
     * @var UpdaterInterface
     */
    protected UpdaterInterface $attributeValueUpdater;

    /**
     * @var ObjectManager
     */
    protected ObjectManager $objectManager;

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
     * @var DateTime|null
     */
    protected $crdate;

    /**
     * @var DateTime|null
     */
    protected $tstamp;

    /**
     * @var \Pixelant\PxaProductManager\Domain\Model\ProductType|null
     */
    protected $productType = null;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Category>
     */
    protected ObjectStorage $categories;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var Product
     */
    protected Product $parent;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Transient
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Product>
     */
    protected ?ObjectStorage $children = null;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Product>
     */
    protected ObjectStorage $relatedProducts;

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
    protected ObjectStorage $attributesFiles;

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
    protected ObjectStorage $attributesValues;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Page>
     */
    protected ObjectStorage $singleviewPage;

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
     * @param MapperServiceInterface $attributesValuesMapper
     */
    public function injectAttributesValuesMapper(MapperServiceInterface $attributesValuesMapper): void
    {
        $this->attributesValuesMapper = $attributesValuesMapper;
    }

    /**
     * @param UpdaterInterface $updaterInterface
     */
    public function injectUpdaterInterface(UpdaterInterface $updaterInterface): void
    {
        $this->attributeValueUpdater = $updaterInterface;
    }

    /**
     * @param ObjectManager $objectManager
     */
    public function injectObjectManager(ObjectManager $objectManager): void
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead.
     *
     * @return void
     */
    protected function initStorageObjects(): void
    {
        $this->categories = new ObjectStorage();
        $this->relatedProducts = new ObjectStorage();
        $this->accessories = new ObjectStorage();
        $this->images = new ObjectStorage();
        $this->attributesFiles = new ObjectStorage();
        $this->links = new ObjectStorage();
        $this->falLinks = new ObjectStorage();
        $this->assets = new ObjectStorage();
        $this->attributesValues = new ObjectStorage();
        $this->singleviewPage = new ObjectStorage();
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
    public function setName(string $name): self
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
    public function setSku(string $sku): self
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
     * Return formatted price.
     *
     * @return string
     */
    public function getFormattedPrice(): string
    {
        return $this->objectManager->get(PriceFormatter::class)->format($this);
    }

    /**
     * @param float $price
     * @return Product
     */
    public function setPrice(float $price): self
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
    public function setTaxRate(float $taxRate): self
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
    public function setTeaser(string $teaser): self
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
    public function setDescription(string $description): self
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
    public function setUsp(string $usp): self
    {
        $this->usp = $usp;

        return $this;
    }

    /**
     * Returns Usp as array split by lines.
     * @return array
     */
    public function getUspArray(): array
    {
        if (!empty($this->usp)) {
            return GeneralUtility::trimExplode(LF, $this->usp, true);
        }

        return [];
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
    public function setAlternativeTitle(string $alternativeTitle): self
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
    public function setKeywords(string $keywords): self
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
    public function setMetaDescription(string $metaDescription): self
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
    public function setDeleted(bool $deleted): self
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
    public function setCrdate(DateTime $crdate): self
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
    public function setTstamp(DateTime $tstamp): self
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
     * Return first product category.
     *
     * @return Category|null
     */
    public function getFirstCategory(): ?Category
    {
        if ($this->categories->count() > 0) {
            $this->categories->rewind();

            return $this->categories->current();
        }

        return null;
    }

    /**
     * @param ObjectStorage $categories
     * @return Product
     */
    public function setCategories(ObjectStorage $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Product|null
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @param Product|null $parent
     */
    public function setParent(?self $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return ObjectStorage
     */
    public function getChildren(): ObjectStorage
    {
        if ($this->children === null) {
            /** @var ProductRepository $productRepository */
            $productRepository = GeneralUtility::makeInstance(ProductRepository::class);
            $query = $productRepository->createQuery();
            $children = $query->matching($query->equals('parent'), $this)->execute();

            $this->children = new ObjectStorage();

            foreach ($children as $child) {
                $this->children->attach($child);
            }
        }

        return $this->children;
    }

    /**
     * Remove the parent.
     */
    public function removeParent(): void
    {
        $this->setParent(null);
    }

    /**
     * @param Product $childProduct
     */
    public function addChild(self $childProduct): void
    {
        $childProduct->setParent($this);
    }

    /**
     * @param Product $childProduct
     */
    public function removeChild(self $childProduct): void
    {
        if ($childProduct->getParent() !== null && $this->getUid() === $childProduct->getParent()->getUid()) {
            $childProduct->setParent(null);
        }
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
    public function setRelatedProducts(ObjectStorage $relatedProducts): self
    {
        $this->relatedProducts = $relatedProducts;

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
    public function setAccessories(ObjectStorage $accessories): self
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
    public function setImages(ObjectStorage $images): self
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getAttributesFiles(): ObjectStorage
    {
        return $this->attributesFiles;
    }

    /**
     * @param ObjectStorage $attributesFiles
     * @return Product
     */
    public function setAttributesFiles(ObjectStorage $attributesFiles): self
    {
        $this->attributesFiles = $attributesFiles;

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
    public function setLinks(ObjectStorage $links): self
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
    public function setFalLinks(ObjectStorage $falLinks): self
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
    public function setAssets(ObjectStorage $assets): self
    {
        $this->assets = $assets;

        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getAttributesValues(): ObjectStorage
    {
        return $this->attributesValues;
    }

    /**
     * @return array
     */
    public function getAttributeValue(): array
    {
        return $this->getCachedProperty('attributeValue', function () {
            $attributeValues = $this->attributesValuesMapper->map($this);

            $keys = array_map(
                fn (AttributeValue $attributeValue) => $attributeValue->getAttribute()->getIdentifier(),
                $attributeValues
            );

            return array_combine($keys, $attributeValues);
        });
    }

    /**
     * Return array of attributes values that has valid attributes.
     *
     * @return array
     */
    public function getAttributesValuesWithValidAttributes(): array
    {
        return $this->collection($this->getAttributesValues())
            ->filter(fn (AttributeValue $attributeValue) => is_object($attributeValue->getAttribute()))
            ->toArray();
    }

    /**
     * @param ObjectStorage $attributesValues
     * @return Product
     */
    public function setAttributesValues(ObjectStorage $attributesValues): self
    {
        $this->attributesValues = $attributesValues;

        return $this;
    }

    /**
     * Will return all attributes sets of all categories rootline.
     * Init attribute values at same time.
     *
     * @return AttributeSet[]
     */
    public function getAttributesSets(): array
    {
        return $this->getCachedProperty('attributesSets', function () {
            return array_values(
                $this->_getAllAttributesSets()
            );
        });
    }

    /**
     * Get array of all product attributes where key is UID or identifier.
     * It will init attributes values.
     *
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        // This will cache result and map attributes values
        $attributesSets = $this->getAttributesSets();

        return $this->getCachedProperty('attributes', function () use ($attributesSets) {
            $attributes = $this->collection($attributesSets)
                ->pluck('attributes')
                ->shiftLevel()
                ->toArray();

            $keys = array_map(
                fn (Attribute $attribute) => $attribute->getIdentifier() ?: $attribute->getUid(),
                $attributes
            );

            return array_combine($keys, $attributes);
        });
    }

    /**
     * Return attribute sets that have at least one attribute with show in listing.
     *
     * @return array
     */
    public function getListingAttributeSets(): array
    {
        return $this->getCachedProperty('listingAttributeSets', function () {
            $attributesSets = $this->getAttributesSets();
            $listingAttributeSets = [];

            foreach ($attributesSets as $attributesSet) {
                $attributeSetHaveNoneEmptyValues = false;

                $attributes = $this->collection($attributesSet->getAttributes())
                    ->filter(fn (Attribute $attribute) => $attribute->getShowInAttributeListing())
                    ->toArray();

                foreach ($attributes as $attribute) {
                    $attributeKey = $attribute->getIdentifier();

                    if (!empty($this->getAttributeValue()[$attributeKey])) {
                        if ($this->getAttributeValue()[$attributeKey]->getHasNonEmptyValue()) {
                            $attributeSetHaveNoneEmptyValues = true;

                            break;
                        }
                    }
                }

                if ($attributeSetHaveNoneEmptyValues) {
                    $listingAttributeSets[] = $attributesSet;
                }
            }

            return $listingAttributeSets;
        });
    }

    /**
     * @return ProductType
     */
    public function getProductType(): ProductType
    {
        return $this->productType;
    }

    /**
     * @param string $productType
     * @return Product
     */
    public function setProductType(string $productType): self
    {
        $this->productType = $productType;

        return $this;
    }

    /**
     * Get all products categories including parents.
     *
     * @return Category[]
     */
    public function getCategoriesWithParents(): array
    {
        // Fetch all parents and merge
        $all = array_merge(...array_map(
            fn (Category $category) => $category->getParentsRootLine(),
            $this->categories->toArray()
        ));

        return $this->collection($all)->unique()->toArray();
    }

    /**
     * Will update attribute value record with new value for given attribute.
     *
     * @param Attribute $attribute
     * @param $value
     */
    public function updateAttributeValue(Attribute $attribute, $value): void
    {
        $this->attributeValueUpdater->update($this, $attribute, $value);
    }

    /**
     * Return listing image if found.
     *
     * @return Image|null
     */
    public function getListImage(): ?Image
    {
        return $this->findImageByType(Image::LISTING_IMAGE) ?? $this->getMainImage();
    }

    /**
     * Return main image if found.
     *
     * @return Image|null
     */
    public function getMainImage(): ?Image
    {
        return $this->findImageByType(Image::MAIN_IMAGE) ?? $this->images->current();
    }

    /**
     * Return images where main image is on first place.
     *
     * @return array
     */
    public function getGalleryImages(): array
    {
        $mainImage = $this->getMainImage();
        if ($mainImage !== null) {
            $sorted = $this->collection($this->images)
                ->filter(fn (Image $image) => $image !== $mainImage)
                ->unshift($mainImage)->toArray();

            return array_values($sorted);
        }

        return $this->images->toArray();
    }

    /**
     * Get navigation title.
     *
     * @return string
     */
    public function getNavigationTitle(): string
    {
        return $this->alternativeTitle ?: $this->name;
    }

    /**
     * Return all attributes sets from choosen productType.
     * It fetch every attribute set of every category from parents tree
     * + product own attributes sets.
     *
     * @return AttributeSet[]
     * @internal Use in BE in order to get all attributes for edit form rendering. Do not use on FE
     * @see getAttributes
     */

    /** @codingStandardsIgnoreStart */
    public function _getAllAttributesSets(): array // @codingStandardsIgnoreEnd
    {
        if (empty($this->productType)) {
            return [];
        }

        $attributesSets = $this->productType->getAttributeSets();

        return array_values(
            $attributesSets->toArray()
        );
    }

    /**
     * Return all attributes sets from choosen productType.
     * It fetch every attribute set of every category from parents tree
     * + product own attributes sets.
     *
     * @return AttributeValues[]
     * @internal Use internally to map all attribute values for frontend rendering.
     * @see getAttributesValues
     */

    /** @codingStandardsIgnoreStart */
    public function _getAllAttributeValues(): array // @codingStandardsIgnoreEnd
    {
        if (empty($this->attributesValues)) {
            return [];
        }

        return array_values(
            $this->attributesValues->toArray()
        );
    }

    /**
     * Find image by type.
     *
     * @return Image|null
     */
    protected function findImageByType(int $type): ?Image
    {
        $images = $this->collection($this->images);
        $image = $images->searchByProperty('type', $type)->first();

        // Reset storage
        $this->images->rewind();

        return $image;
    }

    /**
     * @return ObjectStorage
     */
    public function getSingleviewPage(): ObjectStorage
    {
        return $this->singleviewPage;
    }

    /**
     * Return first product singleviewPage.
     *
     * @return Page|null
     */
    public function getFirstSingleviewPage(): ?Page
    {
        if ($this->singleviewPage->count() > 0) {
            $this->singleviewPage->rewind();

            return $this->singleviewPage->current();
        }

        return null;
    }

    /**
     * @param ObjectStorage $singleviewPage
     * @return Product
     */
    public function setSingleviewPage(ObjectStorage $singleviewPage): self
    {
        $this->singleviewPage = $singleviewPage;

        return $this;
    }
}

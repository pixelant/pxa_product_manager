<?php

namespace Pixelant\PxaProductManager\Utility;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Andriy <andriy@pixelant.se>, Pixelant
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
 ***************************************************************/

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\Category;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class AttributeHelperUtility
 * @package Pixelant\PxaProductManager\Utility
 */
class AttributeHolderUtility
{
    /**
     * Keep all attributes
     *
     * @var ObjectStorage
     */
    protected $attributes;

    /**
     * Keep attributes sorted by sets
     *
     * @var ObjectStorage
     */
    protected $attributeSets;

    /**
     * Initialize
     */
    public function __construct()
    {

        $this->initializeStorage();
    }

    /**
     * Initialize attributes and it sets
     *
     * @param int $productUid
     * @param bool $onlyMarkedForShowInListing
     */
    public function start(int $productUid, bool $onlyMarkedForShowInListing = false)
    {
        $uniqueAttributesList = [];
        $uniqueAttributeSetsList = [];

        $categories = ProductUtility::getProductCategoriesParentsTree($productUid, true);

        /**
         * Find all attribute sets and it unique attributes
         */
        /** @var Category $category */
        foreach ($categories as $category) {
            /** @var AttributeSet $attributesSet */
            foreach ($category->getAttributeSets() as $attributesSet) {
                if (in_array($attributesSet->getUid(), $uniqueAttributeSetsList, true)) {
                    continue;
                } else {
                    $uniqueAttributeSetsList[] = $attributesSet->getUid();
                }

                $currentSetAttributes = new ObjectStorage();

                /** @var Attribute $attribute */
                foreach ($attributesSet->getAttributes() as $attribute) {
                    if (!in_array($attribute->getUid(), $uniqueAttributesList, true)
                        && (
                            !$onlyMarkedForShowInListing
                            || $onlyMarkedForShowInListing && $attribute->isShowInAttributeListing()
                        )
                    ) {
                        // Make sure to use different instances for different products
                        $attributeClone = clone $attribute;
                        // save to current set
                        $currentSetAttributes->attach($attributeClone);
                        // save in all
                        $this->attributes->attach($attributeClone);
                        // save in list
                        $uniqueAttributesList[] = $attribute->getUid();
                    }
                }

                // Save generated attribute set
                $attributesSetClone = clone $attributesSet;
                $attributesSetClone->setAttributes($currentSetAttributes);

                $this->attributeSets->attach($attributesSetClone);
            }
        }
    }

    /**
     * @return ObjectStorage
     */
    public function getAttributes(): ObjectStorage
    {
        return $this->attributes;
    }

    /**
     * @return ObjectStorage
     */
    public function getAttributeSets(): ObjectStorage
    {
        return $this->attributeSets;
    }

    /**
     * Initialize object storage vars
     */
    protected function initializeStorage()
    {
        $this->attributes = new ObjectStorage();
        $this->attributeSets = new ObjectStorage();
    }
}

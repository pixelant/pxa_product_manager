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

use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Order
 * @package Pixelant\PxaProductManager\Domain\Model
 */
class Order extends AbstractDomainObject
{
    /**
     * products
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Product>
     * @lazy
     */
    protected $products = null;

    /**
     * be users
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\BackendUser>
     * @lazy
     */
    protected $seenByBeUsers = null;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $feUser = null;

    /**
     * @var string
     */
    protected $serializedOrderFields = '';

    /**
     * @var string
     */
    protected $serializedProductsQuantity = '';

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
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
        $this->products = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->seenByBeUsers = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Adds a Product
     *
     * @param \Pixelant\PxaProductManager\Domain\Model\Product $product
     * @return void
     */
    public function addProduct(\Pixelant\PxaProductManager\Domain\Model\Product $product)
    {
        $this->products->attach($product);
    }

    /**
     * Removes a Product
     *
     * @param \Pixelant\PxaProductManager\Domain\Model\Product $productToRemove The Product to be removed
     * @return void
     */
    public function removeProduct(\Pixelant\PxaProductManager\Domain\Model\Product $productToRemove)
    {
        $this->products->detach($productToRemove);
    }

    /**
     * Returns the products
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelannt\PxaPixelant\Domain\Model\Product> $products
     */
    public function getProducts(): ObjectStorage
    {
        return $this->products;
    }

    /**
     * Sets the products
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelannt\PxaPixelant\Domain\Model\Product> $products
     * @return void
     */
    public function setProducts(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $products)
    {
        $this->products = $products;
    }

    /**
     * Adds a BE user
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\BackendUser $beUser
     * @return void
     */
    public function addSeenByBeUsers(\TYPO3\CMS\Extbase\Domain\Model\BackendUser $beUser)
    {
        $this->seenByBeUsers->attach($beUser);
    }

    /**
     * Removes a BE user
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\BackendUser $beUser
     * @return void
     */
    public function removeSeenByBeUsers(\TYPO3\CMS\Extbase\Domain\Model\BackendUser $beUser)
    {
        $this->seenByBeUsers->detach($beUser);
    }

    /**
     * Returns the be users
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\BackendUser> $seenByBeUsers
     */
    public function getSeenByBeUsers(): ObjectStorage
    {
        return $this->seenByBeUsers;
    }

    /**
     * Sets the products
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\BackendUser> $seenByBeUsers
     * @return void
     */
    public function setSeenByBeUsers(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $seenByBeUsers)
    {
        $this->seenByBeUsers = $seenByBeUsers;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    public function getFeUser()
    {
        return $this->feUser;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $feUser
     */
    public function setFeUser(FrontendUser $feUser)
    {
        $this->feUser = $feUser;
    }

    /**
     * @return string
     */
    public function getSerializedOrderFields(): string
    {
        return $this->serializedOrderFields;
    }

    /**
     * @param string $serializedOrderFields
     */
    public function setSerializedOrderFields(string $serializedOrderFields)
    {
        $this->serializedOrderFields = $serializedOrderFields;
    }

    /**
     * @return string
     */
    public function getSerializedProductsQuantity(): string
    {
        return $this->serializedProductsQuantity;
    }

    /**
     * @param string $serializedProductsQuantity
     */
    public function setSerializedProductsQuantity(string $serializedProductsQuantity)
    {
        $this->serializedProductsQuantity = $serializedProductsQuantity;
    }

    /**
     * Return product uid to quantity array
     *
     * @return array
     */
    public function getProductsQuantity(): array
    {
        $result = unserialize($this->getSerializedProductsQuantity());

        return is_array($result) ? $result : [];
    }

    /**
     * Save products quantity as serialized string
     *
     * @param array $productsQuantity
     */
    public function setProductsQuantity(array $productsQuantity)
    {
        $this->setSerializedProductsQuantity(
            serialize($productsQuantity)
        );
    }

    /**
     * Get un-serialized order fields information
     *
     * @return array
     */
    public function getOrderFields(): array
    {
        $result = unserialize($this->getSerializedOrderFields());

        return is_array($result) ? $result : [];
    }

    /**
     * Save order fields as serialized string
     *
     * @param array $orderFields
     */
    public function setOrderFields(array $orderFields)
    {
        $this->setSerializedOrderFields(
            serialize($orderFields)
        );
    }
}

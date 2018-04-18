<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Image;
use Pixelant\PxaProductManager\Domain\Model\Link;
use Pixelant\PxaProductManager\Domain\Model\Product;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Test case for class \Pixelant\PxaProductManager\Domain\Model\Product.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Products Manager
 *
 */
class ProductTest extends UnitTestCase
{
    /**
     * @var Product
     */
    protected $fixture;

    /**
     * @var array
     */
    protected $configuration;

    public function setUp()
    {
        $this->fixture = new Product();

        // Set plugin setup ts fetched in "MainUtility" called from Product model
        $GLOBALS['TSFE'] = $this->getMock(
            \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class,
            array(),
            array($GLOBALS['TYPO3_CONF_VARS'], 1, 1)
        );

        $this->configuration = [
            'plugin.' => [
                'tx_pxaproductmanager.' => [
                    'settings.' => [
                        'additionalClasses.' => [
                            'categories.' => [
                                '33' => 'class-33'
                            ],
                            'launched.' => [
                                'isNewClass' => 'class-new'
                            ]
                        ],
                        'launched.' => [
                            'dateIntervalAsNew' => 'P14D'
                        ]
                    ]
                ]
            ]
        ];

        $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_pxaproductmanager.']['settings.'] = [
            'additionalClasses.' => [
                'categories.' => [
                    '33' => 'class-33'
                ],
                'launched.' => [
                    'isNewClass' => 'class-new'
                ]
            ],
            'launched.' => [
                'dateIntervalAsNew' => 'P14D'
            ],
            'customSorting.' => [
                'enable' => 1,
                'points' => [
                    'new' => 50,
                    'categories' => [
                        '2' => 2,
                        '1910' => 1910
                    ]
                ]
            ]
        ];
    }

    public function tearDown()
    {
        unset($GLOBALS['TSFE']);
        unset($this->fixture);
        unset($this->configuration);
    }

    /**
     * @test
     */
    public function getCategoriesReturnsInitialValueForCategories()
    {
        $objectStorage = new ObjectStorage();
        self::assertEquals(
            $objectStorage,
            $this->fixture->getCategories()
        );
    }

    /**
     * @test
     */
    public function setCategoriesForObjectStorageContainingCategoriesSetsCategories()
    {
        $category = new Category();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($category);
        $this->fixture->setCategories($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->fixture->getCategories()
        );
    }

    /**
     * @test
     */
    public function addCategoryForObjectStorageHoldingCategories()
    {
        $category = new Category();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($category);
        $this->fixture->addCategory($category);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getCategories()
        );
    }

    /**
     * @test
     */
    public function removeCategoryForObjectStorageHoldingCategories()
    {
        $category = new Category();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($category);
        $objectStorage->detach($category);
        $this->fixture->addCategory($category);
        $this->fixture->removeCategory($category);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getCategories()
        );
    }

    /**
     * @test
     */
    public function nameCanBeSet()
    {
        $name = 'name';
        $this->fixture->setName($name);

        self::assertEquals(
            $name,
            $this->fixture->getName()
        );
    }

    /**
     * @test
     */
    public function skuCanBeSet()
    {
        $sku = 'sku';
        $this->fixture->setSku($sku);

        self::assertEquals(
            $sku,
            $this->fixture->getSku()
        );
    }

    /**
     * @test
     */
    public function descriptionCanBeSet()
    {
        $description = 'description';
        $this->fixture->setDescription($description);

        self::assertEquals(
            $description,
            $this->fixture->getDescription()
        );
    }

    /**
     * @test
     */
    public function importIdCanBeSet()
    {
        $importId = '123321';
        $this->fixture->setImportId($importId);

        self::assertEquals(
            $importId,
            $this->fixture->getImportId()
        );
    }

    /**
     * @test
     */
    public function importNameCanBeSet()
    {
        $importName = 'importName';
        $this->fixture->setImportName($importName);

        self::assertEquals(
            $importName,
            $this->fixture->getImportName()
        );
    }

    /**
     * @test
     */
    public function disableSingleViewCanBeSet()
    {
        $disableSingleView = true;
        $this->fixture->setDisableSingleView($disableSingleView);

        self::assertEquals(
            $disableSingleView,
            $this->fixture->isDisableSingleView()
        );
    }

    /**
     * @test
     */
    public function alternativeTitleCanBeSet()
    {
        $alternativeTitle = 'alternativeTitle';
        $this->fixture->setAlternativeTitle($alternativeTitle);

        self::assertEquals(
            $alternativeTitle,
            $this->fixture->getAlternativeTitle()
        );
    }

    /**
     * @test
     */
    public function pathSegmentCanBeSet()
    {
        $pathSegment = 'pathSegment';
        $this->fixture->setPathSegment($pathSegment);

        self::assertEquals(
            $pathSegment,
            $this->fixture->getPathSegment()
        );
    }

    /**
     * @test
     */
    public function keyWordsCanBeSet()
    {
        $keyWords = 'keyWords';
        $this->fixture->setKeywords($keyWords);

        self::assertEquals(
            $keyWords,
            $this->fixture->getKeywords()
        );
    }

    /**
     * @test
     */
    public function metaDescriptionCanBeSet()
    {
        $metaDescription = 'metaDescription';
        $this->fixture->setMetaDescription($metaDescription);

        self::assertEquals(
            $metaDescription,
            $this->fixture->getMetaDescription()
        );
    }

    /**
     * @test
     */
    public function getRelatedProductsReturnsInitialValueForRelatedProducts()
    {
        $objectStorage = new ObjectStorage();
        self::assertEquals(
            $objectStorage,
            $this->fixture->getRelatedProducts()
        );
    }

    /**
     * @test
     */
    public function setRelatedProductsForObjectStorageContainingRelatedProductsSetsRelatedProducts()
    {
        $product = new Product();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($product);
        $this->fixture->setRelatedProducts($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->fixture->getRelatedProducts()
        );
    }

    /**
     * @test
     */
    public function addRelateProductForObjectStorageHoldingRelatedProducts()
    {
        $product = new Product();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($product);
        $this->fixture->addRelatedProduct($product);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getRelatedProducts()
        );
    }

    /**
     * @test
     */
    public function removeRelateProductForObjectStorageHoldingRelatedProducts()
    {
        $product = new Product();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($product);
        $objectStorage->detach($product);
        $this->fixture->addRelatedProduct($product);
        $this->fixture->removeRelatedProduct($product);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getRelatedProducts()
        );
    }

    /**
     * @test
     */
    public function getImagesReturnsInitialValueForImages()
    {
        $objectStorage = new ObjectStorage();
        self::assertEquals(
            $objectStorage,
            $this->fixture->getImages()
        );
    }

    /**
     * @test
     */
    public function setImagesForObjectStorageContainingImagesSetsImages()
    {
        $image = new Image();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($image);
        $this->fixture->setImages($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->fixture->getImages()
        );
    }

    /**
     * @test
     */
    public function addImageForObjectStorageHoldingImages()
    {
        $image = new Image();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($image);
        $this->fixture->addImage($image);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getImages()
        );
    }

    /**
     * @test
     */
    public function removeImageForObjectStorageHoldingImages()
    {
        $image = new Image();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($image);
        $objectStorage->detach($image);
        $this->fixture->addImage($image);
        $this->fixture->removeImage($image);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getImages()
        );
    }

    /**
     * @test
     */
    public function getMainProductImageFromObjectStorageHoldingImages()
    {
        $image = new Image();
        $image->setMainImage(true);

        $this->fixture->addImage($image);

        self::assertSame(
            $image,
            $this->fixture->getMainImage()
        );
    }

    /**
     * @test
     */
    public function getThumbnailProductImageFromObjectStorageHoldingImages()
    {
        $image = new Image();
        $image->setUseInListing(true);

        $this->fixture->addImage($image);

        self::assertSame(
            $image,
            $this->fixture->getThumbnail()
        );
    }

    /**
     * @test
     */
    public function getAttributeImagesReturnsInitialValueForAttributeImages()
    {
        $objectStorage = new ObjectStorage();
        self::assertEquals(
            $objectStorage,
            $this->fixture->getAttributeImages()
        );
    }

    /**
     * @test
     */
    public function setAttributeImagesForObjectStorageContainingAttributeImagesSetsAttributeImages()
    {
        $image = new Image();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($image);
        $this->fixture->setAttributeImages($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->fixture->getAttributeImages()
        );
    }

    /**
     * @test
     */
    public function addAttributeImageForObjectStorageHoldingAttributeImages()
    {
        $image = new Image();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($image);
        $this->fixture->addAttributeImage($image);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getAttributeImages()
        );
    }

    /**
     * @test
     */
    public function removeAttributeImageForObjectStorageHoldingAttributeImages()
    {
        $image = new Image();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($image);
        $objectStorage->detach($image);
        $this->fixture->addAttributeImage($image);
        $this->fixture->removeAttributeImage($image);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getAttributeImages()
        );
    }

    /**
     * @test
     */
    public function getLinksReturnsInitialValueForLinks()
    {
        $objectStorage = new ObjectStorage();
        self::assertEquals(
            $objectStorage,
            $this->fixture->getLinks()
        );
    }

    /**
     * @test
     */
    public function setLinksForObjectStorageContainingLinksSetsLinks()
    {
        $link = new Link();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($link);
        $this->fixture->setLinks($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->fixture->getLinks()
        );
    }

    /**
     * @test
     */
    public function addLinkForObjectStorageHoldingLinks()
    {
        $link = new Link();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($link);
        $this->fixture->addLink($link);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getLinks()
        );
    }

    /**
     * @test
     */
    public function removeLinkForObjectStorageHoldingLinks()
    {
        $link = new Link();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($link);
        $objectStorage->detach($link);
        $this->fixture->addLink($link);
        $this->fixture->removeLink($link);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getLinks()
        );
    }

    /**
     * @test
     */
    public function getSubProductsReturnsInitialValueForSubProducts()
    {
        $objectStorage = new ObjectStorage();
        self::assertEquals(
            $objectStorage,
            $this->fixture->getSubProducts()
        );
    }

    /**
     * @test
     */
    public function setSubProductsForObjectStorageContainingSubProductsSetsSubProducts()
    {
        $product = new Product();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($product);
        $this->fixture->setSubProducts($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->fixture->getSubProducts()
        );
    }

    /**
     * @test
     */
    public function addSubProductForObjectStorageHoldingSubProducts()
    {
        $product = new Product();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($product);
        $this->fixture->addSubProduct($product);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getSubProducts()
        );
    }

    /**
     * @test
     */
    public function removeSubProductForObjectStorageHoldingSubProducts()
    {
        $product = new Product();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($product);
        $objectStorage->detach($product);
        $this->fixture->addSubProduct($product);
        $this->fixture->removeSubProduct($product);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getSubProducts()
        );
    }

    /**
     * @test
     */
    public function getFalLinksReturnsInitialValueForFalLinks()
    {
        $objectStorage = new ObjectStorage();
        self::assertEquals(
            $objectStorage,
            $this->fixture->getFalLinks()
        );
    }

    /**
     * @test
     */
    public function setFalLinksForObjectStorageContainingFalLinksSetsFalLinks()
    {
        $falLink = new FileReference();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($falLink);
        $this->fixture->setFalLinks($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->fixture->getFalLinks()
        );
    }

    /**
     * @test
     */
    public function addFalLinkForObjectStorageHoldingFalLinks()
    {
        $falLink = new FileReference();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($falLink);
        $this->fixture->addFalLink($falLink);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getFalLinks()
        );
    }

    /**
     * @test
     */
    public function removeFalLinkForObjectStorageHoldingFalLinks()
    {
        $falLink = new FileReference();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($falLink);
        $objectStorage->detach($falLink);
        $this->fixture->addFalLink($falLink);
        $this->fixture->removeFalLink($falLink);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getFalLinks()
        );
    }

    /**
     * @test
     */
    public function getAttributesReturnsInitialValueForAttributes()
    {
        $objectStorage = new ObjectStorage();
        self::assertEquals(
            $objectStorage,
            $this->fixture->getAttributes()
        );
    }

    /**
     * @test
     */
    public function setAttributesForObjectStorageContainingAttributesSetsAttributes()
    {
        $attribute = new Attribute();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($attribute);
        $this->fixture->setAttributes($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->fixture->getAttributes()
        );
    }

    /**
     * @test
     */
    public function addAttributeForObjectStorageHoldingAttributes()
    {
        $attribute = new Attribute();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($attribute);
        $this->fixture->addAttribute($attribute);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getAttributes()
        );
    }

    /**
     * @test
     */
    public function removeAttributeForObjectStorageHoldingAttributes()
    {
        $attribute = new Attribute();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($attribute);
        $objectStorage->detach($attribute);
        $this->fixture->addAttribute($attribute);
        $this->fixture->removeAttribute($attribute);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getAttributes()
        );
    }

    /**
     * @test
     */
    public function getAttributeValueUsingMagicCallByNameFromObjectStorageHoldingAttributes()
    {
        $attribute = new Attribute();
        $value = 'magic-value';
        $attribute->setName('TestName');
        $attribute->setValue($value);

        $this->fixture->addAttribute($attribute);

        self::assertSame(
            $value,
            $this->fixture->getTestName()->getValue()
        );
    }

    /**
     * @test
     */
    public function getAttributeValueUsingMagicCallByIdentifierFromObjectStorageHoldingAttributes()
    {
        $attribute = new Attribute();
        $value = 'magic-value';
        $attribute->setIdentifier('UniqueIdentifier');
        $attribute->setValue($value);

        $this->fixture->addAttribute($attribute);

        self::assertSame(
            $value,
            $this->fixture->getUniqueIdentifier()->getValue()
        );
    }

    /**
     * @test
     */
    public function getAttributeValuesReturnsInitialValueForAttributeValues()
    {
        $objectStorage = new ObjectStorage();
        self::assertEquals(
            $objectStorage,
            $this->fixture->getAttributeValues()
        );
    }

    /**
     * @test
     */
    public function setAttributeValuesForObjectStorageContainingAttributeValuesSetsAttributeValues()
    {
        $attributeValue = new AttributeValue();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($attributeValue);
        $this->fixture->setAttributeValues($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->fixture->getAttributeValues()
        );
    }

    /**
     * @test
     */
    public function addAttributeValueForObjectStorageHoldingAttributeValues()
    {
        $attributeValue = new AttributeValue();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($attributeValue);
        $this->fixture->addAttributeValue($attributeValue);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getAttributeValues()
        );
    }

    /**
     * @test
     */
    public function removeAttributeValueForObjectStorageHoldingAttributeValues()
    {
        $attributeValue = new AttributeValue();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($attributeValue);
        $objectStorage->detach($attributeValue);
        $this->fixture->addAttributeValue($attributeValue);
        $this->fixture->removeAttributeValue($attributeValue);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getAttributeValues()
        );
    }

    /**
     * @test
     */
    public function crdateCanBeSet()
    {
        $crdate = 1500454777453;
        $this->fixture->setCrdate($crdate);

        self::assertEquals(
            $crdate,
            $this->fixture->getCrdate()
        );
    }

    /**
     * @test
     */
    public function tstampCaBeSet()
    {
        $tstamp = 1500454846848;
        $this->fixture->setTstamp($tstamp);

        self::assertEquals(
            $tstamp,
            $this->fixture->getTstamp()
        );
    }

    /**
     * @test
     */
    public function hiddenCaBeSet()
    {
        $hidden = true;
        $this->fixture->setHidden($hidden);

        self::assertEquals(
            $hidden,
            $this->fixture->getHidden()
        );
    }

    /**
     * @test
     */
    public function deletedCaBeSet()
    {
        $deleted = true;
        $this->fixture->setDeleted($deleted);

        self::assertEquals(
            $deleted,
            $this->fixture->getDeleted()
        );
    }

    /**
     * @test
     */
    public function serializedAttributesValuesCanBeSet()
    {
        $serializedAttributesValues = serialize(['test']);
        $this->fixture->setSerializedAttributesValues($serializedAttributesValues);

        self::assertEquals(
            $serializedAttributesValues,
            $this->fixture->getSerializedAttributesValues()
        );
    }

    /**
     * @test
     */
    public function attributesDescriptionCanBeSet()
    {
        $attributeDescription = 'attributeDescription';
        $this->fixture->setAttributeDescription($attributeDescription);

        self::assertEquals(
            $description,
            $this->fixture->getAttributeDescription()
        );
    }

    /**
     * @test
     */
    public function getAccessoriesReturnsInitialValueForAccessories()
    {
        $objectStorage = new ObjectStorage();
        self::assertEquals(
            $objectStorage,
            $this->fixture->getAccessories()
        );
    }

    /**
     * @test
     */
    public function setAccessoriesForObjectStorageContainingAccessoriesSetsAccessories()
    {
        $product = new Product();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($product);
        $this->fixture->setAccessories($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->fixture->getAccessories()
        );
    }

    /**
     * @test
     */
    public function addAccesoryForObjectStorageHoldingAccessories()
    {
        $product = new Product();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($product);
        $this->fixture->addAccessory($product);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getAccessories()
        );
    }

    /**
     * @test
     */
    public function removeAccessoryForObjectStorageHoldingAccessories()
    {
        $product = new Product();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($product);
        $objectStorage->detach($product);
        $this->fixture->addAccessory($product);
        $this->fixture->removeAccessory($product);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getAccessories()
        );
    }

    /**
     * @test
     */
    public function launchedCanBeSet()
    {
        $tstamp = new \DateTime();
        $this->fixture->setLaunched($tstamp);

        self::assertEquals(
            $tstamp,
            $this->fixture->getLaunched()
        );
    }

    /**
     * @test
     */
    public function discontinuedCanBeSet()
    {
        $tstamp = new \DateTime();
        $this->fixture->setDiscontinued($tstamp);

        self::assertEquals(
            $tstamp,
            $this->fixture->getDiscontinued()
        );
    }

    /**
     * @test
     */
    public function uspCanBeSet()
    {
        $usp = 'unique selling point';
        $this->fixture->setUsp($usp);

        self::assertEquals(
            $usp,
            $this->fixture->getUsp()
        );
    }

    /**
     * @test
     */
    public function uspCanBeGetAsList()
    {
        $usp = implode(
            "\n",
            [
                'unique selling point 1',
                'unique selling point 2'
            ]
        );
        $uspAsList = explode("\n", $usp);

        $this->fixture->setUsp($usp);

        self::assertEquals(
            $uspAsList,
            $this->fixture->getUspAsList()
        );
    }

    /**
     * @test
     */
    public function teaserCanBeSet()
    {
        $teaser = 'teaser';
        $this->fixture->setTeaser($teaser);

        self::assertEquals(
            $teaser,
            $this->fixture->getTeaser()
        );
    }

    /**
     * @test
     */
    public function additionalInformationCanBeSet()
    {
        $additionalInformation = 'additional information';
        $this->fixture->setAdditionalInformation($additionalInformation);

        self::assertEquals(
            $additionalInformation,
            $this->fixture->getAdditionalInformation()
        );
    }

    /**
     * @test
     */
    public function getAssetsReturnsInitialValueForAssets()
    {
        $objectStorage = new ObjectStorage();
        self::assertEquals(
            $objectStorage,
            $this->fixture->getAssets()
        );
    }

    /**
     * @test
     */
    public function setAssetsForObjectStorageContainingAssetsSetsAssets()
    {
        $asset = new FileReference();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($asset);
        $this->fixture->setAssets($objectStorage);

        self::assertSame(
            $objectStorage,
            $this->fixture->getAssets()
        );
    }

    /**
     * @test
     */
    public function addAssetForObjectStorageHoldingAssets()
    {
        $asset = new FileReference();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($asset);
        $this->fixture->addAsset($asset);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getAssets()
        );
    }

    /**
     * @test
     */
    public function removeAssetForObjectStorageHoldingAssets()
    {
        $asset = new FileReference();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($asset);
        $objectStorage->detach($asset);
        $this->fixture->addAsset($asset);
        $this->fixture->removeAsset($asset);

        self::assertEquals(
            $objectStorage,
            $this->fixture->getAssets()
        );
    }

    /**
     * @test
     */
    public function getIsNewCanBeFetched()
    {
        $configurationUtilityMock = $this->getMockBuilder(\Pixelant\PxaProductManager\Utility\ConfigurationUtility::class)
            ->setMethods(['getSettings'])
            ->getMock();
        $configurationManagerMock = $this->getMockBuilder(\Pixelant\PxaProductManager\Configuration\ConfigurationManager::class)
            ->setMethods(['getConfiguration'])
            ->getMock();
        $environmentServiceMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Service\EnvironmentService::class)
            ->setMethods(array('isEnvironmentInFrontendMode'))
            ->getMock();

        ObjectAccess::setProperty($configurationUtilityMock, 'configurationManager', $configurationManagerMock, true);
        ObjectAccess::setProperty($configurationManagerMock, 'environmentService', $environmentServiceMock, true);

        $environmentServiceMock->method('isEnvironmentInFrontendMode')->will($this->returnValue(false));
        $configurationManagerMock->method('getConfiguration')->will($this->returnValue($this->configuration));

        // verify that getIsNew returns false when we set launched to today -15 days
        $launched = new \DateTime();
        $launched->modify('-15 days');
        $this->fixture->setLaunched($launched);

        self::assertEquals(
            false,
            $this->fixture->getIsNew()
        );

        // verify that getIsNew returns true when we set launched to today -13 days
        $launched = new \DateTime();
        $launched->modify('-13 days');
        $this->fixture->setLaunched($launched);

        self::assertEquals(
            true,
            $this->fixture->getIsNew()
        );
    }

    /**
     * @test
     */
    public function getAdditionalClassesCanBeFetched()
    {
        $category = $this->getMockBuilder(Category::class)
            ->setMethods(['getUid'])
            ->disableOriginalConstructor()
            ->getMock();
        $category->method('getUid')->will(self::returnValue(33));
        $this->fixture->addCategory($category);

        $configurationUtilityMock = $this->getMockBuilder(\Pixelant\PxaProductManager\Utility\ConfigurationUtility::class)
            ->setMethods(['getSettings'])
            ->getMock();
        $configurationManagerMock = $this->getMockBuilder(\Pixelant\PxaProductManager\Configuration\ConfigurationManager::class)
            ->setMethods(['getConfiguration'])
            ->getMock();
        $environmentServiceMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Service\EnvironmentService::class)
            ->setMethods(array('isEnvironmentInFrontendMode'))
            ->getMock();

        ObjectAccess::setProperty($configurationUtilityMock, 'configurationManager', $configurationManagerMock, true);
        ObjectAccess::setProperty($configurationManagerMock, 'environmentService', $environmentServiceMock, true);

        $environmentServiceMock->method('isEnvironmentInFrontendMode')->will($this->returnValue(false));
        $configurationManagerMock->method('getConfiguration')->will($this->returnValue($this->configuration));

        // verify that getAdditionalClasses returns correct classe for category
        $launched = new \DateTime();
        $launched->modify('-15 days');
        $this->fixture->setLaunched($launched);

        self::assertEquals(
            'class-33',
            $this->fixture->getAdditionalClasses()
        );

        // verify that getAdditionalClasses returns correct classes for "new" and category
        $launched = new \DateTime();
        $launched->modify('-13 days');
        $this->fixture->setLaunched($launched);

        self::assertEquals(
            'class-33 class-new',
            $this->fixture->getAdditionalClasses()
        );
    }

    /**
     * @test
     */
    public function customSortingCanBeSet()
    {
        $customSorting = 1910;
        $this->fixture->setCustomSorting($customSorting);

        self::assertEquals(
            $customSorting,
            $this->fixture->getCustomSorting()
        );
    }
}

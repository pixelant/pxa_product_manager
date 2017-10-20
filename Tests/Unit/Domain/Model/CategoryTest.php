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
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Image;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;

/**
 * Test case for class \Pixelant\PxaProductManager\Domain\Model\Category.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Products Manager
 *
 */
class CategoryTest extends UnitTestCase
{
    /**
     * @var Category
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new Category();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function parentCanBeSet()
    {
        $parentCategory = new Category();
        $this->fixture->setParent($parentCategory);

        self::assertSame(
            $parentCategory,
            $this->fixture->getParent()
        );
    }

    /**
     * @test
     */
    public function alternativeTitleCanBeSet()
    {
        $altTitle = 'title';
        $this->fixture->setAlternativeTitle($altTitle);

        self::assertEquals(
            $altTitle,
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
    public function keywordsCanBeSet()
    {
        $keywords = 'keywords';
        $this->fixture->setKeywords($keywords);

        self::assertEquals(
            $keywords,
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
     * This also test pxaImage
     *
     * @test
     */
    public function imageCanBeSet()
    {
        $image = new Image();
        $this->fixture->setImage($image);

        self::assertSame(
            $image,
            $this->fixture->getImage()
        );
    }

    /**
     * @test
     */
    public function hiddenCanBeSet()
    {
        $hidden = false;
        $this->fixture->setHidden($hidden);

        self::assertEquals(
            $hidden,
            $this->fixture->getHidden()
        );
    }

    /**
     * @test
     */
    public function deletedCanBeSet()
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
    public function getAttributeSetReturnsInitialForAttributeSet()
    {
        $objectStorage = new ObjectStorage();

        self::assertEquals(
            $objectStorage,
            $this->fixture->getAttributeSets()
        );
    }

    /**
     * @test
     */
    public function setAttributeSetForObjectStorageContainsAttributeSetForAttibuteSets()
    {
        $objectStorageWithOneAttribute = new ObjectStorage();

        $attribute = new AttributeSet();
        $objectStorageWithOneAttribute->attach($attribute);
        $this->fixture->setAttributeSets($objectStorageWithOneAttribute);

        self::assertSame(
            $objectStorageWithOneAttribute,
            $this->fixture->getAttributeSets()
        );
    }

    /**
     * @test
     */
    public function addAttributeSetToObjectStorageHoldingAttributeSet()
    {
        $attribute = new AttributeSet();
        $objectStorageWithOneAttribute = new ObjectStorage();
        $objectStorageWithOneAttribute->attach($attribute);

        $this->fixture->addAttributeSet($attribute);

        self::assertEquals(
            $objectStorageWithOneAttribute,
            $this->fixture->getAttributeSets()
        );
    }

    /**
     * @test
     */
    public function removeAttributeSetFromObjectStorageHoldingAttributeSets()
    {
        $attribute = new AttributeSet();
        $objectStorageWithOneAttribute = new ObjectStorage();
        $objectStorageWithOneAttribute->attach($attribute);
        $objectStorageWithOneAttribute->detach($attribute);
        $this->fixture->addAttributeSet($attribute);
        $this->fixture->removeAttributeSet($attribute);

        self::assertEquals(
            $objectStorageWithOneAttribute,
            $this->fixture->getAttributeSets()
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
}

<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017
 *
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
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Filter;

/**
 * Test case for class \Pixelant\PxaProductManager\Domain\Model\Filter.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class FilterTest extends UnitTestCase
{
    /**
     * @var Filter
     */
    protected $fixture = null;

    public function setUp()
    {
        $this->fixture = new Filter();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function typeCanBeSet()
    {
        $type = 1;
        $this->fixture->setType($type);

        self::assertEquals(
            $type,
            $this->fixture->getType()
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
    public function parentCategoryCanBeSet()
    {
        $category = new Category();
        $this->fixture->setParentCategory($category);

        self::assertSame(
            $category,
            $this->fixture->getParentCategory()
        );
    }

    /**
     * @test
     */
    public function attributeCanBeSet()
    {
        $attribute = new Attribute();
        $this->fixture->setAttribute($attribute);

        self::assertSame(
            $attribute,
            $this->fixture->getAttribute()
        );
    }
}

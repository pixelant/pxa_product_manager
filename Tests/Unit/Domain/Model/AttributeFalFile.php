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
use Pixelant\PxaProductManager\Domain\Model\AttributeFalFile;

/**
 * Test case for class \Pixelant\PxaProductManager\Domain\Model\Image.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Products Manager
 *
 */
class AttributeFalFileTest extends UnitTestCase
{
    /**
     * @var AttributeFalFile
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new AttributeFalFile();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function defaultAttributeValueIsZero()
    {
        $this->assertEquals(0, $this->fixture->getAttribute());
    }

    /**
     * @test
     */
    public function attributeCanBeSet()
    {
        $pxaAttribute = 111;
        $this->fixture->setAttribute($pxaAttribute);

        $this->assertEquals(
            $pxaAttribute,
            $this->fixture->getAttribute()
        );
    }
}

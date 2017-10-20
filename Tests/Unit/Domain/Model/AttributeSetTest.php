<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Test case for class \Pixelant\PxaProductManager\Domain\Model\AttributeSet.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Products Manager
 *
 */
class AttributeSetTest extends UnitTestCase
{
    /**
     * @var AttributeSet
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new AttributeSet();
    }

    public function tearDown()
    {
        unset($this->fixture);
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
    public function getAttributeReturnsInitialForAttribute()
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
    public function setAttributeForObjectStorageContainsAttributeForAttibute()
    {
        $objectStorageWithOneAttribute = new ObjectStorage();

        $attribute = new Attribute();
        $objectStorageWithOneAttribute->attach($attribute);
        $this->fixture->setAttributes($objectStorageWithOneAttribute);

        self::assertSame(
            $objectStorageWithOneAttribute,
            $this->fixture->getAttributes()
        );
    }

    /**
     * @test
     */
    public function addAttributeToObjectStorageHoldingAttributes()
    {
        $attribute = new Attribute();
        $objectStorageWithOneAttribute = new ObjectStorage();
        $objectStorageWithOneAttribute->attach($attribute);

        $this->fixture->addAttribute($attribute);

        self::assertEquals(
            $objectStorageWithOneAttribute,
            $this->fixture->getAttributes()
        );
    }

    /**
     * @test
     */
    public function removeAttributeFromObjectStorageHoldingAttributes()
    {
        $attribute = new Attribute();
        $objectStorageWithOneAttribute = new ObjectStorage();
        $objectStorageWithOneAttribute->attach($attribute);
        $objectStorageWithOneAttribute->detach($attribute);
        $this->fixture->addAttribute($attribute);
        $this->fixture->removeAttribute($attribute);

        self::assertEquals(
            $objectStorageWithOneAttribute,
            $this->fixture->getAttributes()
        );
    }
}

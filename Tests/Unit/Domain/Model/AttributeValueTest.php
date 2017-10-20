<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Product;

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
class AttributeValueTest extends UnitTestCase
{
    /**
     * @var AttributeValue
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new AttributeValue();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function getValueReturnsInitialValueForString()
    {
        self::assertEquals(
            '',
            $this->fixture->getValue()
        );
    }

    /**
     * @test
     */
    public function valueCanBeSet()
    {
        $this->fixture->setValue('AttributeValue');

        self::assertEquals(
            'AttributeValue',
            $this->fixture->getValue()
        );
    }

    /**
     * @test
     */
    public function productCanBeSet()
    {
        $product = new Product();
        $this->fixture->setProduct($product);

        self::assertSame(
            $product,
            $this->fixture->getProduct()
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

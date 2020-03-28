<?php
declare(strict_types=1);
namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueMapper;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueMapper\GeneralMapper;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Product;


/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Adapter\Attributes
 */
class GeneralMapperTest extends UnitTestCase
{
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = new GeneralMapper();
    }

    /**
     * @test
     */
    public function searchAttributeValueReturnNullIfValueNotFound()
    {
        $attribute = createEntity(Attribute::class, 1);
        $product = createEntity(Product::class, 1);

        $this->assertNull($this->callInaccessibleMethod($this->subject, 'searchAttributeValue', $product, $attribute));
    }

    /**
     * @test
     */
    public function searchAttributeValueReturnCorrespondingForAttributeValue()
    {
        $attribute1 = createEntity(Attribute::class, 10);
        $attribute2 = createEntity(Attribute::class, 50);
        $attribute3 = createEntity(Attribute::class, 100);

        $attributeValue1 = createEntity(AttributeValue::class, ['uid' => 1, 'attribute' => $attribute1]);
        $attributeValue2 = createEntity(AttributeValue::class, ['uid' => 2, 'attribute' => $attribute2]);
        $attributeValue3 = createEntity(AttributeValue::class, ['uid' => 2, 'attribute' => $attribute3]);

        /** @var Product $product */
        $product = createEntity(Product::class, 1);
        $product->setAttributesValues(createObjectStorage($attributeValue1, $attributeValue2, $attributeValue3));

        $this->assertSame(
            $attributeValue3,
            $this->callInaccessibleMethod($this->subject, 'searchAttributeValue', $product, $attribute3)
        );
    }

    /**
     * @test
     */
    public function mapWillSetValueOfAttributeValueForAttribute()
    {
        $attributeValue = $this->prophesize(AttributeValue::class);
        $attributeValue->getValue()->shouldBeCalled()->willReturn('testvalue');

        $attribute = createEntity(Attribute::class, 1);
        $product = createEntity(Product::class, 1);

        $adapter = $this->createPartialMock(GeneralMapper::class, ['searchAttributeValue']);
        $adapter->expects($this->once())->method('searchAttributeValue')->willReturn($attributeValue->reveal());

        $adapter->map($product, $attribute);

        $this->assertEquals('testvalue', $attribute->getValue());
    }
}

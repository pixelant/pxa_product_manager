<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Adapter\Attributes\ValueMapper;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueMapper\DateTimeMapper;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Adapter\Attributes\ValueMapper
 */
class SelectBoxMapperTest extends UnitTestCase
{
    /**
     * @test
     */
    public function mapWillConvertDateStringToDateObjectAndSetAsValue()
    {
        $mapper = $this->createPartialMock(DateTimeMapper::class, ['searchAttributeValue']);

        $attributeValue = $this->prophesize(AttributeValue::class);
        $attributeValue->getValue()->shouldBeCalled()->willReturn('now');

        $attribute = createEntity(Attribute::class, 1);
        $product = createEntity(Product::class, 1);

        $mapper->expects($this->once())->method('searchAttributeValue')->willReturn($attributeValue->reveal());
        $mapper->map($product, $attribute);

        $this->assertInstanceOf(\DateTime::class, $attribute->getValue());
    }

    /**
     * @test
     */
    public function mapWillSetNullOnInvalidDate()
    {
        $mapper = $this->createPartialMock(DateTimeMapper::class, ['searchAttributeValue']);

        $attributeValue = $this->prophesize(AttributeValue::class);
        $attributeValue->getValue()->shouldBeCalled()->willReturn('');

        $attribute = createEntity(Attribute::class, 1);
        $product = createEntity(Product::class, 1);

        $mapper->expects($this->once())->method('searchAttributeValue')->willReturn($attributeValue->reveal());
        $mapper->map($product, $attribute);

        $this->assertNull(\DateTime::class, $attribute->getValue());
    }
}

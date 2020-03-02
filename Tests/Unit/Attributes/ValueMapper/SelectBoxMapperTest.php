<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Adapter\Attributes\ValueMapper;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueMapper\GeneralMapper;
use Pixelant\PxaProductManager\Attributes\ValueMapper\SelectBoxMapper;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Option;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Adapter\Attributes\ValueMapper
 */
class SelectBoxMapperTest extends UnitTestCase
{
    /**
     * @test
     */
    public function mapWillSetMatchingOptionsAsValueOfAttribute()
    {
        $mapper = $this->createPartialMock(SelectBoxMapper::class, ['searchAttributeValue']);

        $option1 = createEntity(Option::class, 1);
        $option2 = createEntity(Option::class, 2);
        $option3 = createEntity(Option::class, 3);
        $option4 = createEntity(Option::class, 4);

        $attributeValue = $this->prophesize(AttributeValue::class);
        $attributeValue->getValue()->shouldBeCalled()->willReturn('2,3');

        $attribute = createEntity(Attribute::class, 1);
        $attribute->setOptions(createObjectStorage($option1, $option2, $option3, $option4));

        $product = createEntity(Product::class, 1);

        $mapper->expects($this->once())->method('searchAttributeValue')->willReturn($attributeValue->reveal());
        $mapper->map($product, $attribute);

        $this->assertEquals([$option2, $option3], $attribute->getValue());
    }
}

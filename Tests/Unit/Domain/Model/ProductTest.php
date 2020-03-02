<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Attributes\ValueMapper\MapperService;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model
 */
class ProductTest extends UnitTestCase
{
    /**
     * @var Product
     */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = new Product();
    }

    /**
     * @test
     */
    public function getAttributesSetsWillValues()
    {
        $am = $this->prophesize(MapperService::class);
        $am->map($this->subject)->shouldBeCalled();

        $this->subject->injectAttributesValuesMapper($am->reveal());

        $this->subject->getAttributesSets();
    }

    /**
     * @test
     */
    public function getAttributesSetCacheValues()
    {
        $am = $this->prophesize(MapperService::class);
        $am->map($this->subject)->shouldBeCalledOnce();

        $this->subject->injectAttributesValuesMapper($am->reveal());

        $this->subject->getAttributesSets();
        $this->subject->getAttributesSets();
        $this->subject->getAttributesSets();
    }

    /**
     * @test
     */
    public function getAttributesReturnAllAttributesFromAttAttributesSets()
    {
        $attribute1 = createEntity(Attribute::class, 1);
        $attribute2 = createEntity(Attribute::class, 2);
        $attribute3 = createEntity(Attribute::class, 3);

        $attributeSet1 = createEntity(AttributeSet::class, 1);
        $attributeSet2 = createEntity(AttributeSet::class, 2);

        $attributeSet1->setAttributes(createObjectStorage($attribute1));
        $attributeSet2->setAttributes(createObjectStorage($attribute2, $attribute3));

        $product = $this->createPartialMock(Product::class, ['getAttributesSets']);
        $product->expects($this->once())->method('getAttributesSets')->willReturn([$attributeSet1, $attributeSet2]);

        $expect = [$attribute1, $attribute2, $attribute3];

        $this->assertEquals($expect, $product->getAttributes());
    }

    /**
     * @test
     */
    public function getOwnAttributesSetsReturnProductAttributesSets()
    {
        $sets = createObjectStorage(...createMultipleEntities(AttributeSet::class, 2));
        $this->subject->setAttributesSets($sets);

        $this->assertSame($sets, $this->subject->getOwnAttributesSets());
    }
}

<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueMapper;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueMapper\GeneralMapper;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;

class GeneralMapperTest extends UnitTestCase
{
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new GeneralMapper();
    }

    /**
     * @test
     */
    public function searchAttributeValueReturnNullIfValueNotFound(): void
    {
        $attribute = TestsUtility::createEntity(Attribute::class, 1);
        $product = TestsUtility::createEntity(Product::class, 1);

        self::assertNull($this->callInaccessibleMethod($this->subject, 'searchAttributeValue', $product, $attribute));
    }

    /**
     * @test
     */
    public function searchAttributeValueReturnCorrespondingForAttributeValue(): void
    {
        $attribute1 = TestsUtility::createEntity(Attribute::class, 10);
        $attribute2 = TestsUtility::createEntity(Attribute::class, 50);
        $attribute3 = TestsUtility::createEntity(Attribute::class, 100);

        $attributeValue1 = TestsUtility::createEntity(AttributeValue::class, ['uid' => 1, 'attribute' => $attribute1]);
        $attributeValue2 = TestsUtility::createEntity(AttributeValue::class, ['uid' => 2, 'attribute' => $attribute2]);
        $attributeValue3 = TestsUtility::createEntity(AttributeValue::class, ['uid' => 2, 'attribute' => $attribute3]);

        /** @var Product $product */
        $product = TestsUtility::createEntity(Product::class, 1);
        $product->setAttributesValues(
            TestsUtility::createObjectStorage($attributeValue1, $attributeValue2, $attributeValue3)
        );

        self::assertSame(
            $attributeValue3,
            $this->callInaccessibleMethod($this->subject, 'searchAttributeValue', $product, $attribute3)
        );
    }

    /**
     * @test
     */
    public function mapWillSetValueOfAttributeValueForAttribute(): void
    {
        $attributeValue = $this->prophesize(AttributeValue::class);
        $attributeValue->getValue()->shouldBeCalled()->willReturn('testvalue');

        $attribute = TestsUtility::createEntity(Attribute::class, 1);
        $product = TestsUtility::createEntity(Product::class, 1);

        $adapter = $this->createPartialMock(GeneralMapper::class, ['searchAttributeValue']);
        $adapter->expects(self::once())->method('searchAttributeValue')->willReturn($attributeValue->reveal());

        $adapter->map($product, $attribute);

        self::assertEquals('testvalue', $attribute->getValue());
    }
}

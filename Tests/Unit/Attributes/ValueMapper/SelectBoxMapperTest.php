<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueMapper;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueMapper\SelectBoxMapper;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Option;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;

class SelectBoxMapperTest extends UnitTestCase
{
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new SelectBoxMapper();
    }

    /**
     * @test
     */
    public function mapWillSetMatchingOptionsAsValueOfAttribute(): void
    {
        $option1 = TestsUtility::createEntity(Option::class, 1);
        $option2 = TestsUtility::createEntity(Option::class, 2);
        $option3 = TestsUtility::createEntity(Option::class, 3);
        $option4 = TestsUtility::createEntity(Option::class, 4);

        $attribute = TestsUtility::createEntity(Attribute::class, 1);
        $attribute->setType(Attribute::ATTRIBUTE_TYPE_MULTISELECT);
        $attribute->setOptions(TestsUtility::createObjectStorage($option1, $option2, $option3, $option4));

        $attributeValue = TestsUtility::createEntity(AttributeValue::class, 5);
        $attributeValue->setValue(',2,3,');
        $attributeValue->setAttribute($attribute);

        $product = TestsUtility::createEntity(Product::class, 1);

        $this->subject->map($product, $attributeValue);

        self::assertEquals([$option2, $option3], $attributeValue->getArrayValue());
    }
}

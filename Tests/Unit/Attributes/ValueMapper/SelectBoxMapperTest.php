<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueMapper;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Option;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;

class SelectBoxMapperTest extends UnitTestCase
{
    /**
     * @test
     */
    public function mapWillSetMatchingOptionsAsValueOfAttribute(): void
    {
        /** @var Product $product */
        $product = TestsUtility::createEntity(Product::class, 1);

        /** @var Option $option1 */
        $option1 = TestsUtility::createEntity(Option::class, 1);
        /** @var Option $option2 */
        $option2 = TestsUtility::createEntity(Option::class, 2);
        /** @var Option $option3 */
        $option3 = TestsUtility::createEntity(Option::class, 3);
        /** @var Option $option4 */
        $option4 = TestsUtility::createEntity(Option::class, 4);

        /** @var Attribute $attribute */
        $attribute = TestsUtility::createEntity(Attribute::class, 1);
        $attribute->setType(Attribute::ATTRIBUTE_TYPE_MULTISELECT);
        $attribute->setOptions(TestsUtility::createObjectStorage($option1, $option2, $option3, $option4));

        /** @var AttributeValue $attributeValue */
        $attributeValue = TestsUtility::createEntity(AttributeValue::class, 5);
        $attributeValue->setValue(',2,3,');
        $attributeValue->setAttribute($attribute);
        $attributeValue->setProduct($product);

        self::assertEquals([$option2, $option3], $attributeValue->getArrayValue());
    }
}

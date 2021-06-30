<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;

class AttributeValueTest extends UnitTestCase
{
    /**
     * @test
     */
    public function usingAsStringReturnValue(): void
    {
        $testStringValue = 'SomeString';

        /** @var Product $product */
        $product = TestsUtility::createEntity(Product::class, 2);

        /** @var Attribute $attribute */
        $attribute = TestsUtility::createEntity(Attribute::class, 3);
        $attribute->setType(Attribute::ATTRIBUTE_TYPE_INPUT);

        /** @var AttributeValue $attributeValue */
        $attributeValue = TestsUtility::createEntity(AttributeValue::class, 1);
        $attributeValue->setValue($testStringValue);
        $attributeValue->setProduct($product);
        $attributeValue->setAttribute($attribute);

        self::assertEquals($testStringValue, (string)$attributeValue->getStringValue());
    }
}

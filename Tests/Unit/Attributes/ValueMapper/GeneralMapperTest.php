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
    public function mapWillSetValueOfAttributeValueForProductAttributeValue(): void
    {
        $attributeProperties = ['uid' => 1, 'product' => 2, 'attribute' => 3, 'value' => 'testvalue'];
        $attributeValue = TestsUtility::createEntity(AttributeValue::class, $attributeProperties);

        $attribute = TestsUtility::createEntity(Attribute::class, 3);
        $attribute->setType(Attribute::ATTRIBUTE_TYPE_INPUT);

        $product = TestsUtility::createEntity(Product::class, 2);

        $this->subject->map($product, $attributeValue);

        self::assertEquals('testvalue', $attributeValue->getStringValue());
    }
}

<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueMapper;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueMapper\FalMapper;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeFile;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;

class FalMapperTest extends UnitTestCase
{
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new FalMapper();
    }

    /**
     * @test
     */
    public function mapWillSetMatchingFilesAsValueOfAttribute(): void
    {
        $attributeFile1 = TestsUtility::createEntity(AttributeFile::class, ['uid' => 1, 'attribute' => 10]);
        $attributeFile2 = TestsUtility::createEntity(AttributeFile::class, ['uid' => 2, 'attribute' => 10]);
        $attributeFile3 = TestsUtility::createEntity(AttributeFile::class, ['uid' => 2, 'attribute' => 20]);

        $attribute = TestsUtility::createEntity(Attribute::class, 10);
        $attribute->setType(Attribute::ATTRIBUTE_TYPE_FILE);

        $attributeValue = TestsUtility::createEntity(AttributeValue::class, 10);
        $attributeValue->setAttribute($attribute);

        /** @var Product $product */
        $product = TestsUtility::createEntity(Product::class, 1);
        $product->setAttributesFiles(
            TestsUtility::createObjectStorage($attributeFile1, $attributeFile2, $attributeFile3)
        );

        $this->subject->map($product, $attributeValue);

        self::assertEquals([$attributeFile1, $attributeFile2], $attributeValue->getArrayValue());
    }
}

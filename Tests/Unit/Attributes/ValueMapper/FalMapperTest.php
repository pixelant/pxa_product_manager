<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueMapper;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueMapper\FalMapper;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeFile;
use Pixelant\PxaProductManager\Domain\Model\Product;

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
        $attributeFile1 = createEntity(AttributeFile::class, ['uid' => 1, 'attribute' => 10]);
        $attributeFile2 = createEntity(AttributeFile::class, ['uid' => 2, 'attribute' => 10]);
        $attributeFile3 = createEntity(AttributeFile::class, ['uid' => 2, 'attribute' => 20]);

        $attribute = createEntity(Attribute::class, 10);
        $attribute->setType(Attribute::ATTRIBUTE_TYPE_FILE);

        /** @var Product $product */
        $product = createEntity(Product::class, 1);
        $product->setAttributesFiles(createObjectStorage($attributeFile1, $attributeFile2, $attributeFile3));

        $this->subject->map($product, $attribute);

        self::assertEquals([$attributeFile1, $attributeFile2], $attribute->getValue());
    }
}

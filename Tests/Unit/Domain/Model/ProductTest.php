<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueMapper\MapperService;
use Pixelant\PxaProductManager\Attributes\ValueUpdater\ValueUpdaterService;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Image;
use Pixelant\PxaProductManager\Domain\Model\Product;

class ProductTest extends UnitTestCase
{
    /**
     * @var Product
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Product();
    }

    /**
     * @test
     */
    public function getAttributesSetsWillValues(): void
    {
        $am = $this->prophesize(MapperService::class);
        $am->map($this->subject)->shouldBeCalled();

        $this->subject->injectAttributesValuesMapper($am->reveal());

        $this->subject->getAttributesSets();
    }

    /**
     * @test
     */
    public function getAttributesSetCacheValues(): void
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
    public function getAttributesReturnAllAttributesFromAttAttributesSetsWhereKeysAreIdentifierOrUid(): void
    {
        $attribute1 = createEntity(Attribute::class, ['uid' => 1, 'identifier' => 'first']);
        $attribute2 = createEntity(Attribute::class, ['uid' => 2, 'identifier' => 'second']);
        $attribute3 = createEntity(Attribute::class, 3);

        $attributeSet1 = createEntity(AttributeSet::class, 1);
        $attributeSet2 = createEntity(AttributeSet::class, 2);

        $attributeSet1->setAttributes(createObjectStorage($attribute1));
        $attributeSet2->setAttributes(createObjectStorage($attribute2, $attribute3));

        $product = $this->createPartialMock(Product::class, ['getAttributesSets']);
        $product->expects(self::once())->method('getAttributesSets')->willReturn([$attributeSet1, $attributeSet2]);

        $expect = [
            'first' => $attribute1,
            'second' => $attribute2,
            3 => $attribute3,
        ];

        self::assertEquals($expect, $product->getAttributes());
    }

    /**
     * @test
     */
    public function productCanUpdateAttributeValue(): void
    {
        $attribute = createEntity(Attribute::class, 1);
        $newValue = 'new value';

        $updater = $this->prophesize(ValueUpdaterService::class);
        $updater->update($this->subject, $attribute, $newValue)->shouldBeCalled();

        $this->subject->injectUpdaterInterface($updater->reveal());

        $this->subject->updateAttributeValue($attribute, $newValue);
    }

    /**
     * @test
     */
    public function findImageByTypeReturnMatchedImage(): void
    {
        $image1 = createEntity(Image::class, ['uid' => 1]);
        $image2 = createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);
        $image3 = createEntity(Image::class, ['uid' => 3, 'type' => Image::LISTING_IMAGE]);

        $this->subject->setImages(createObjectStorage($image1, $image2, $image3));

        self::assertSame($image2, $this->callInaccessibleMethod($this->subject, 'findImageByType', Image::MAIN_IMAGE));
    }

    /**
     * @test
     */
    public function findImageByTypeReturnNullIfNoMatchedImage(): void
    {
        $image1 = createEntity(Image::class, ['uid' => 1, 'type' => 0]);
        $image2 = createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);
        $image3 = createEntity(Image::class, ['uid' => 3]);

        $this->subject->setImages(createObjectStorage($image1, $image2, $image3));

        self::assertNull($this->callInaccessibleMethod($this->subject, 'findImageByType', Image::LISTING_IMAGE));
    }

    /**
     * @test
     */
    public function getMainImageReturnMainImage(): void
    {
        $image1 = createEntity(Image::class, ['uid' => 1]);
        $image2 = createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);
        $image3 = createEntity(Image::class, ['uid' => 3, 'type' => Image::LISTING_IMAGE]);

        $this->subject->setImages(createObjectStorage($image1, $image2, $image3));

        self::assertSame($image2, $this->subject->getMainImage());
    }

    /**
     * @test
     */
    public function getMainImageReturnFirstImageIfNotFound(): void
    {
        $image1 = createEntity(Image::class, ['uid' => 1]);
        $image2 = createEntity(Image::class, ['uid' => 2, 'type' => 0]);
        $image3 = createEntity(Image::class, ['uid' => 3, 'type' => Image::LISTING_IMAGE]);

        $this->subject->setImages(createObjectStorage($image1, $image2, $image3));

        self::assertSame($image1, $this->subject->getMainImage());
    }

    /**
     * @test
     */
    public function getListImageReturnListImageIfFound(): void
    {
        $image1 = createEntity(Image::class, ['uid' => 1]);
        $image2 = createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);
        $image3 = createEntity(Image::class, ['uid' => 3, 'type' => Image::LISTING_IMAGE]);

        $this->subject->setImages(createObjectStorage($image1, $image2, $image3));

        self::assertSame($image3, $this->subject->getListImage());
    }

    /**
     * @test
     */
    public function getListImageReturnMainImageIfNoListingAndMainExistFound(): void
    {
        $image1 = createEntity(Image::class, ['uid' => 1]);
        $image2 = createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);

        $this->subject->setImages(createObjectStorage($image1, $image2));

        self::assertSame($image2, $this->subject->getListImage());
    }

    /**
     * @test
     */
    public function getListImageReturnFisrtImageIfNoFound(): void
    {
        $image1 = createEntity(Image::class, 1);
        $image2 = createEntity(Image::class, 2);

        $this->subject->setImages(createObjectStorage($image1, $image2));

        self::assertSame($image1, $this->subject->getListImage());
    }

    /**
     * @test
     */
    public function getGalleryImagesReturnImagesWhereMainImageIsOnFirstPlace(): void
    {
        $image1 = createEntity(Image::class, 1);
        $image2 = createEntity(Image::class, 2);
        $image3 = createEntity(Image::class, ['uid' => 3, 'type' => Image::MAIN_IMAGE]);

        $this->subject->setImages(createObjectStorage($image1, $image2, $image3));
        $expect = [$image3, $image1, $image2];

        self::assertEquals($expect, $this->subject->getGalleryImages());
    }

    /**
     * @test
     */
    public function getFirstCategoryReturnFirstCategory(): void
    {
        $cat1 = createEntity(Category::class, 1);
        $cat2 = createEntity(Category::class, 2);

        $this->subject->setCategories(createObjectStorage($cat1, $cat2));

        self::assertSame($cat1, $this->subject->getFirstCategory());
    }

    /**
     * @test
     */
    public function getListingAttributesReturnOnlyAttributesThatAreVisibleInListing(): void
    {
        $attr1 = createEntity(Attribute::class, ['uid' => 1, 'showInAttributeListing' => false]);
        $attr2 = createEntity(Attribute::class, ['uid' => 2, 'showInAttributeListing' => false]);
        $attr3 = createEntity(Attribute::class, ['uid' => 3, 'showInAttributeListing' => true]);

        $subject = $this->createPartialMock(Product::class, ['getAttributes']);
        $subject->expects(self::once())->method('getAttributes')->willReturn([1 => $attr1, 2 => $attr2, 3 => $attr3]);

        self::assertEquals([3 => $attr3], $subject->getListingAttributes());
    }

    /**
     * @test
     */
    public function getNavigationTitleReturnAlternativeTitleIfExist(): void
    {
        $this->subject->setAlternativeTitle('title');

        self::assertEquals('title', $this->subject->getNavigationTitle());
    }

    /**
     * @test
     */
    public function getNavigationTitleReturnNameIfNoAlternativeTitle(): void
    {
        $this->subject->setName('name');

        self::assertEquals('name', $this->subject->getNavigationTitle());
    }

    /**
     * @test
     */
    public function getAttributesValuesWithValidAttributesReturnAttributesValuesThatHasAttributes(): void
    {
        $attributeValue = createEntity(AttributeValue::class, 1);
        $attributeValue->setAttribute(createEntity(Attribute::class, 100));

        $attributeValue2 = createEntity(AttributeValue::class, 2);

        $subject = $this->createPartialMock(Product::class, ['getAttributesValues']);
        $subject->expects(self::once())->method('getAttributesValues')->willReturn(createObjectStorage($attributeValue, $attributeValue2));

        self::assertEquals([$attributeValue], array_values($subject->getAttributesValuesWithValidAttributes()));
    }
}

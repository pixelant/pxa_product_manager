<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueUpdater\ValueUpdaterService;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Image;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Model\ProductType;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;

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
    public function getAttributesReturnAllAttributesFromAttAttributesSetsWhereKeysAreIdentifierOrUid(): void
    {
        $attribute1 = TestsUtility::createEntity(Attribute::class, ['uid' => 1, 'identifier' => 'first']);
        $attribute2 = TestsUtility::createEntity(Attribute::class, ['uid' => 2, 'identifier' => 'second']);
        $attribute3 = TestsUtility::createEntity(Attribute::class, 3);

        $attributeSet1 = TestsUtility::createEntity(AttributeSet::class, 1);
        $attributeSet2 = TestsUtility::createEntity(AttributeSet::class, 2);

        $attributeSet1->setAttributes(TestsUtility::createObjectStorage($attribute1));
        $attributeSet2->setAttributes(TestsUtility::createObjectStorage($attribute2, $attribute3));

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
        $attribute = TestsUtility::createEntity(Attribute::class, 1);
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
        $image1 = TestsUtility::createEntity(Image::class, ['uid' => 1]);
        $image2 = TestsUtility::createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);
        $image3 = TestsUtility::createEntity(Image::class, ['uid' => 3, 'type' => Image::LISTING_IMAGE]);

        $this->subject->setImages(TestsUtility::createObjectStorage($image1, $image2, $image3));

        self::assertSame($image2, $this->callInaccessibleMethod($this->subject, 'findImageByType', Image::MAIN_IMAGE));
    }

    /**
     * @test
     */
    public function findImageByTypeReturnNullIfNoMatchedImage(): void
    {
        $image1 = TestsUtility::createEntity(Image::class, ['uid' => 1, 'type' => 0]);
        $image2 = TestsUtility::createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);
        $image3 = TestsUtility::createEntity(Image::class, ['uid' => 3]);

        $this->subject->setImages(TestsUtility::createObjectStorage($image1, $image2, $image3));

        self::assertNull($this->callInaccessibleMethod($this->subject, 'findImageByType', Image::LISTING_IMAGE));
    }

    /**
     * @test
     */
    public function getMainImageReturnMainImage(): void
    {
        $image1 = TestsUtility::createEntity(Image::class, ['uid' => 1]);
        $image2 = TestsUtility::createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);
        $image3 = TestsUtility::createEntity(Image::class, ['uid' => 3, 'type' => Image::LISTING_IMAGE]);

        $this->subject->setImages(TestsUtility::createObjectStorage($image1, $image2, $image3));

        self::assertSame($image2, $this->subject->getMainImage());
    }

    /**
     * @test
     */
    public function getMainImageReturnFirstImageIfNotFound(): void
    {
        $image1 = TestsUtility::createEntity(Image::class, ['uid' => 1]);
        $image2 = TestsUtility::createEntity(Image::class, ['uid' => 2, 'type' => 0]);
        $image3 = TestsUtility::createEntity(Image::class, ['uid' => 3, 'type' => Image::LISTING_IMAGE]);

        $this->subject->setImages(TestsUtility::createObjectStorage($image1, $image2, $image3));

        self::assertSame($image1, $this->subject->getMainImage());
    }

    /**
     * @test
     */
    public function getListImageReturnListImageIfFound(): void
    {
        $image1 = TestsUtility::createEntity(Image::class, ['uid' => 1]);
        $image2 = TestsUtility::createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);
        $image3 = TestsUtility::createEntity(Image::class, ['uid' => 3, 'type' => Image::LISTING_IMAGE]);

        $this->subject->setImages(TestsUtility::createObjectStorage($image1, $image2, $image3));

        self::assertSame($image3, $this->subject->getListImage());
    }

    /**
     * @test
     */
    public function getListImageReturnMainImageIfNoListingAndMainExistFound(): void
    {
        $image1 = TestsUtility::createEntity(Image::class, ['uid' => 1]);
        $image2 = TestsUtility::createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);

        $this->subject->setImages(TestsUtility::createObjectStorage($image1, $image2));

        self::assertSame($image2, $this->subject->getListImage());
    }

    /**
     * @test
     */
    public function getListImageReturnFisrtImageIfNoFound(): void
    {
        $image1 = TestsUtility::createEntity(Image::class, 1);
        $image2 = TestsUtility::createEntity(Image::class, 2);

        $this->subject->setImages(TestsUtility::createObjectStorage($image1, $image2));

        self::assertSame($image1, $this->subject->getListImage());
    }

    /**
     * @test
     */
    public function getGalleryImagesReturnImagesWhereMainImageIsOnFirstPlace(): void
    {
        $image1 = TestsUtility::createEntity(Image::class, 1);
        $image2 = TestsUtility::createEntity(Image::class, 2);
        $image3 = TestsUtility::createEntity(Image::class, ['uid' => 3, 'type' => Image::MAIN_IMAGE]);

        $this->subject->setImages(TestsUtility::createObjectStorage($image1, $image2, $image3));
        $expect = [$image3, $image1, $image2];

        self::assertEquals($expect, $this->subject->getGalleryImages());
    }

    /**
     * @test
     */
    public function getFirstCategoryReturnFirstCategory(): void
    {
        $cat1 = TestsUtility::createEntity(Category::class, 1);
        $cat2 = TestsUtility::createEntity(Category::class, 2);

        $this->subject->setCategories(TestsUtility::createObjectStorage($cat1, $cat2));

        self::assertSame($cat1, $this->subject->getFirstCategory());
    }

    /**
     * @test
     */
    public function getListingAttributesSetsReturnAttributesSetsWithThatHaveAttributesThatAreVisibleInListing(): void
    {
        $testStringValue = 'SomeString';
        /** @var Product $product */
        $product = TestsUtility::createEntity(Product::class, 2000);

        /** @var Attribute $attribute1 */
        $attribute1 = TestsUtility::createEntity(Attribute::class, 1);
        $attribute1->setType(Attribute::ATTRIBUTE_TYPE_INPUT);
        $attribute1->setShowInAttributeListing(false);

        /** @var Attribute $attribute2 */
        $attribute2 = TestsUtility::createEntity(Attribute::class, 2);
        $attribute2->setType(Attribute::ATTRIBUTE_TYPE_INPUT);
        $attribute2->setShowInAttributeListing(false);

        /** @var Attribute $attribute3 */
        $attribute3 = TestsUtility::createEntity(Attribute::class, 3);
        $attribute3->setType(Attribute::ATTRIBUTE_TYPE_INPUT);
        $attribute3->setShowInAttributeListing(true);

        /** @var AttributeSet $attributeSet1 */
        $attributeSet1 = TestsUtility::createEntity(AttributeSet::class, 10);
        $attributeSet1->setAttributes(TestsUtility::createObjectStorage($attribute1));

        /** @var AttributeSet $attributeSet2 */
        $attributeSet2 = TestsUtility::createEntity(AttributeSet::class, 20);
        $attributeSet2->setAttributes(TestsUtility::createObjectStorage($attribute2));

        /** @var AttributeSet $attributeSet3 */
        $attributeSet3 = TestsUtility::createEntity(AttributeSet::class, 30);
        $attributeSet3->setAttributes(TestsUtility::createObjectStorage($attribute3));

        /** @var ProductType $productType */
        $productType = TestsUtility::createEntity(ProductType::class, 1000);
        $productType->setAttributeSets(
            TestsUtility::createObjectStorage($attributeSet1, $attributeSet2, $attributeSet3)
        );
        $product->setProductType($productType);

        /** @var AttributeValue $attributeValue */
        $attributeValue = TestsUtility::createEntity(AttributeValue::class, 100);
        $attributeValue->setValue($testStringValue);
        $attributeValue->setProduct($product);
        $attributeValue->setAttribute($attribute3);

        $subject = $this->createPartialMock(
            Product::class,
            [
                '_getAllAttributesSets',
                'getAttributeValue',
            ]
        );
        $subject->expects(self::once())->method('_getAllAttributesSets')->willReturn(
            [
                10 => $attributeSet1,
                20 => $attributeSet2,
                30 => $attributeSet3,
            ]
        );

        $subject->method('getAttributeValue')->willReturn([3 => $attributeValue]);

        self::assertEquals([0 => $attributeSet3], $subject->getListingAttributeSets());
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
    public function getUspArrayReturnsArray(): void
    {
        $usp = [
            'Line 1',
            'Line 2',
            'Line 3',
            'Line 4',
        ];

        $this->subject->setUsp(implode(PHP_EOL, $usp));

        self::assertEquals($usp, $this->subject->getUspArray());
    }

    /**
     * @test
     */
    public function getAttributesValuesWithValidAttributesReturnAttributesValuesThatHasAttributes(): void
    {
        $attributeValue = TestsUtility::createEntity(AttributeValue::class, 1);
        $attributeValue->setAttribute(TestsUtility::createEntity(Attribute::class, 100));

        $attributeValue2 = TestsUtility::createEntity(AttributeValue::class, 2);

        $subject = $this->createPartialMock(Product::class, ['getAttributesValues']);
        $subject
            ->expects(self::once())
            ->method('getAttributesValues')
            ->willReturn(TestsUtility::createObjectStorage($attributeValue, $attributeValue2));

        self::assertEquals([$attributeValue], array_values($subject->getAttributesValuesWithValidAttributes()));
    }
}

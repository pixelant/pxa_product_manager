<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueUpdater\ValueUpdaterService;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Image;
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
    public function getAttributesReturnAllAttributesFromAttAttributesSetsWhereKeysAreIdentifierOrUid()
    {
        $attribute1 = createEntity(Attribute::class, ['uid' => 1, 'identifier' => 'first']);
        $attribute2 = createEntity(Attribute::class, ['uid' => 2, 'identifier' => 'second']);
        $attribute3 = createEntity(Attribute::class, 3);

        $attributeSet1 = createEntity(AttributeSet::class, 1);
        $attributeSet2 = createEntity(AttributeSet::class, 2);

        $attributeSet1->setAttributes(createObjectStorage($attribute1));
        $attributeSet2->setAttributes(createObjectStorage($attribute2, $attribute3));

        $product = $this->createPartialMock(Product::class, ['getAttributesSets']);
        $product->expects($this->once())->method('getAttributesSets')->willReturn([$attributeSet1, $attributeSet2]);

        $expect = [
            'first' => $attribute1,
            'second' => $attribute2,
            3 => $attribute3
        ];

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

    /**
     * @test
     */
    public function productCanUpdateAttributeValue()
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
    public function findImageByTypeReturnMatchedImage()
    {
        $image1 = createEntity(Image::class, ['uid' => 1]);
        $image2 = createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);
        $image3 = createEntity(Image::class, ['uid' => 3, 'type' => Image::LISTING_IMAGE]);

        $this->subject->setImages(createObjectStorage($image1, $image2, $image3));

        $this->assertSame($image2, $this->callInaccessibleMethod($this->subject, 'findImageByType', Image::MAIN_IMAGE));
    }

    /**
     * @test
     */
    public function findImageByTypeReturnNullIfNoMatchedImage()
    {
        $image1 = createEntity(Image::class, ['uid' => 1, 'type' => 0]);
        $image2 = createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);
        $image3 = createEntity(Image::class, ['uid' => 3]);

        $this->subject->setImages(createObjectStorage($image1, $image2, $image3));

        $this->assertNull($this->callInaccessibleMethod($this->subject, 'findImageByType', Image::LISTING_IMAGE));
    }

    /**
     * @test
     */
    public function getMainImageReturnMainImage()
    {
        $image1 = createEntity(Image::class, ['uid' => 1]);
        $image2 = createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);
        $image3 = createEntity(Image::class, ['uid' => 3, 'type' => Image::LISTING_IMAGE]);

        $this->subject->setImages(createObjectStorage($image1, $image2, $image3));

        $this->assertSame($image2, $this->subject->getMainImage());
    }

    /**
     * @test
     */
    public function getMainImageReturnFirstImageIfNotFound()
    {
        $image1 = createEntity(Image::class, ['uid' => 1]);
        $image2 = createEntity(Image::class, ['uid' => 2, 'type' => 0]);
        $image3 = createEntity(Image::class, ['uid' => 3, 'type' => Image::LISTING_IMAGE]);

        $this->subject->setImages(createObjectStorage($image1, $image2, $image3));

        $this->assertSame($image1, $this->subject->getMainImage());
    }

    /**
     * @test
     */
    public function getListImageReturnListImageIfFound()
    {
        $image1 = createEntity(Image::class, ['uid' => 1]);
        $image2 = createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);
        $image3 = createEntity(Image::class, ['uid' => 3, 'type' => Image::LISTING_IMAGE]);

        $this->subject->setImages(createObjectStorage($image1, $image2, $image3));

        $this->assertSame($image3, $this->subject->getListImage());
    }

    /**
     * @test
     */
    public function getListImageReturnMainImageIfNoListingAndMainExistFound()
    {
        $image1 = createEntity(Image::class, ['uid' => 1]);
        $image2 = createEntity(Image::class, ['uid' => 2, 'type' => Image::MAIN_IMAGE]);

        $this->subject->setImages(createObjectStorage($image1, $image2));

        $this->assertSame($image2, $this->subject->getListImage());
    }

    /**
     * @test
     */
    public function getListImageReturnFisrtImageIfNoFound()
    {
        $image1 = createEntity(Image::class, 1);
        $image2 = createEntity(Image::class, 2);

        $this->subject->setImages(createObjectStorage($image1, $image2));

        $this->assertSame($image1, $this->subject->getListImage());
    }

    /**
     * @test
     */
    public function getGalleryImagesReturnImagesWhereMainImageIsOnFirstPlace()
    {
        $image1 = createEntity(Image::class, 1);
        $image2 = createEntity(Image::class, 2);
        $image3 = createEntity(Image::class, ['uid' => 3, 'type' => Image::MAIN_IMAGE]);

        $this->subject->setImages(createObjectStorage($image1, $image2, $image3));
        $expect = [$image3, $image1, $image2];

        $this->assertEquals($expect, $this->subject->getGalleryImages());
    }

    /**
     * @test
     */
    public function getFirstCategoryReturnFirstCategory()
    {
        $cat1 = createEntity(Category::class, 1);
        $cat2 = createEntity(Category::class, 2);

        $this->subject->setCategories(createObjectStorage($cat1, $cat2));

        $this->assertSame($cat1, $this->subject->getFirstCategory());
    }

    /**
     * @test
     */
    public function getListingAttributesReturnOnlyAttributesThatAreVisibleInListing()
    {
        $attr1 = createEntity(Attribute::class, ['uid' => 1, 'showInAttributeListing' => false]);
        $attr2 = createEntity(Attribute::class, ['uid' => 2, 'showInAttributeListing' => false]);
        $attr3 = createEntity(Attribute::class, ['uid' => 3, 'showInAttributeListing' => true]);

        $subject = $this->createPartialMock(Product::class, ['getAttributes']);
        $subject->expects($this->once())->method('getAttributes')->willReturn([1 => $attr1, 2 => $attr2, 3 => $attr3]);

        $this->assertEquals([3 => $attr3], $subject->getListingAttributes());
    }

    /**
     * @test
     */
    public function getNavigationTitleReturnAlternativeTitleIfExist()
    {
        $this->subject->setAlternativeTitle('title');

        $this->assertEquals('title', $this->subject->getNavigationTitle());
    }

    /**
     * @test
     */
    public function getNavigationTitleReturnNameIfNoAlternativeTitle()
    {
        $this->subject->setName('name');

        $this->assertEquals('name', $this->subject->getNavigationTitle());
    }
}

<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model
 */
class ProductTest extends UnitTestCase
{
    /**
     * @var Product
     */
    protected $product;

    protected function setUp()
    {
        parent::setUp();

        $this->product = new Product();
    }

    /**
     * @test
     * @dataProvider getAllAttributeSetsDataProvider
     */
    public function getAllAttributesSetsReturnAttributesSetsOfCategoriesAndProductInCorrectOrder($categories, $attributesSets, $expect)
    {
        $this->product->setCategories($categories);
        $this->product->setAttributesSets($attributesSets);

        $this->assertEquals($expect, $this->product->getAllAttributesSets());
    }

    /**
     * @test
     */
    public function getCategoriesWithParentsReturnAllRootLine()
    {
        $cat1 = makeDomainInstanceWithProperties(Category::class, 1);
        $cat2 = makeDomainInstanceWithProperties(Category::class, 2);
        $cat3 = makeDomainInstanceWithProperties(Category::class, 3);
        $cat4 = makeDomainInstanceWithProperties(Category::class, 4);
        $cat5 = makeDomainInstanceWithProperties(Category::class, 5);

        $cat2->setParent($cat1);
        $cat4->setParent($cat3);
        $cat5->setParent($cat3);

        $expect = [$cat2, $cat1, $cat4, $cat3, $cat5];
        $this->product->setCategories(createObjectStorageWithObjects(
            $cat2, $cat4, $cat5
        ));

        $this->assertEquals($expect, $this->product->getCategoriesWithParents());
    }

    public function getAllAttributeSetsDataProvider()
    {
        $attributesSet1 = makeDomainInstanceWithProperties(AttributeSet::class, 1);
        $attributesSet2 = makeDomainInstanceWithProperties(AttributeSet::class, 2);
        $attributesSet3 = makeDomainInstanceWithProperties(AttributeSet::class, 3);
        $attributesSet4 = makeDomainInstanceWithProperties(AttributeSet::class, 4);
        $attributesSet5 = makeDomainInstanceWithProperties(AttributeSet::class, 5);

        $category1WithAttributesSet2_3 = makeDomainInstanceWithProperties(Category::class, 1, fn(Category $category) => $category->addAttributeSet($attributesSet2)->addAttributeSet($attributesSet3));
        $category2WithAttributesSet2_4 = makeDomainInstanceWithProperties(Category::class, 2, fn(Category $category) => $category->addAttributeSet($attributesSet2)->addAttributeSet($attributesSet4));

        $subCategory = makeDomainInstanceWithProperties(Category::class, 99, fn(Category $category) => $category->addAttributeSet($attributesSet5));
        $subCategory->setParent($category2WithAttributesSet2_4);

        return [
            'has_duplicated_attributes' => [
                'categories' => createObjectStorageWithObjects($category1WithAttributesSet2_3, $subCategory),
                'attributesSets' => createObjectStorageWithObjects($attributesSet4, $attributesSet1),
                // First goes attributes sets of product, then categories
                'result' => [$attributesSet4, $attributesSet1, $attributesSet2, $attributesSet3, $attributesSet5],
            ],
        ];
    }
}

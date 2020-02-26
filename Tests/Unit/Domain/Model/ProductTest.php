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

    public function getAllAttributeSetsDataProvider()
    {
        $attributesSet1 = createDomainInstanceWithProperties(AttributeSet::class, 1);
        $attributesSet2 = createDomainInstanceWithProperties(AttributeSet::class, 2);
        $attributesSet3 = createDomainInstanceWithProperties(AttributeSet::class, 3);
        $attributesSet4 = createDomainInstanceWithProperties(AttributeSet::class, 4);

        $category1WithAttributesSet2_3 = createDomainInstanceWithProperties(Category::class, 1, fn(Category $category) => $category->addAttributeSet($attributesSet2)->addAttributeSet($attributesSet3));
        $category2WithAttributesSet2_4 = createDomainInstanceWithProperties(Category::class, 2, fn(Category $category) => $category->addAttributeSet($attributesSet2)->addAttributeSet($attributesSet4));

        return [
            'has_duplicated_attributes' => [
                'categories' => createObjectStorageWithObjects($category1WithAttributesSet2_3, $category2WithAttributesSet2_4),
                'attributesSets' => createObjectStorageWithObjects($attributesSet4, $attributesSet1),
                // First goes attributes sets of product, then categories
                'result' => [$attributesSet4, $attributesSet1, $attributesSet2, $attributesSet3],
            ],
        ];
    }
}

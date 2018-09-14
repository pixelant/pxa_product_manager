<?php
namespace Pixelant\PxaProductManager\Tests\Utility;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Utility\CategoryUtility;
use Pixelant\PxaProductManager\Domain\Model\Category;

/**
 * Class ProductUtilityTest
 * @package Pixelant\PxaProductManager\Tests\Utility
 */
class CategoryUtilityTest extends UnitTestCase
{
    /**
     * @test
     */
    public function getParentCategoriesWithHightLevelThrowException()
    {
        $category = new Category();

        $this->expectException(\RuntimeException::class);
        CategoryUtility::getParentCategories($category, [], 51);
    }
}

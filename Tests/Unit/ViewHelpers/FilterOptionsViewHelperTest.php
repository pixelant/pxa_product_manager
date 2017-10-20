<?php
namespace Pixelant\PxaProductManager\Tests\Unit\ViewHelpers;

use Nimut\TestingFramework\TestCase\ViewHelperBaseTestcase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Filter;
use Pixelant\PxaProductManager\Domain\Model\Option;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\ViewHelpers\FilterOptionsViewHelper;

/**
 * Class FilterOptionsViewHelperTest
 * @package Pixelant\PxaProductManager\Tests\ViewHelpers
 */
class FilterOptionsViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var FilterOptionsViewHelper
     */
    protected $viewHelper;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->viewHelper = $this->getAccessibleMock(FilterOptionsViewHelper::class, ['dummy']);
        $this->categoryRepository = $this->prophesize(CategoryRepository::class);
        $this->viewHelper->initializeArguments();
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->viewHelper, $this->categoryRepository);
    }

    /**
     * @test
     */
    public function getFilterOptionsByTypeCategyExpectCategoriesResult()
    {
        $filter = new Filter();
        $parentCategory = new Category();

        $category1 = new Category();
        $category1->_setProperty('uid', 123);
        $category1->setTitle('category 1');

        $category2 = new Category();
        $category2->_setProperty('uid', 321);
        $category2->setTitle('category 2');

        $expected = [
            [
                'title' => $category1->getTitle(),
                'value' => $category1->getUid()
            ],
            [
                'title' => $category2->getTitle(),
                'value' => $category2->getUid()
            ],
        ];


        $filter->setType(Filter::TYPE_CATEGORIES);
        $filter->setParentCategory($parentCategory);

        $this->categoryRepository->findByParent($parentCategory)->willReturn([$category1, $category2]);

        $this->viewHelper->_set('categoryRepository', $this->categoryRepository->reveal());
        $this->viewHelper->_set('arguments', ['filter' => $filter]);

        $this->categoryRepository->findByParent($parentCategory)->shouldBeCalled();

        self::assertEquals(
            $expected,
            $this->viewHelper->render()
        );
    }

    /**
     * @test
     */
    public function getFilterOptionsByTypeAttributesExpectAttributesResult()
    {
        $filter = new Filter();

        $option1 = new Option();
        $option1->_setProperty('uid', 123);
        $option1->setValue('value123');

        $option2 = new Option();
        $option2->_setProperty('uid', 321);
        $option2->setValue('value321');

        $attribute = new Attribute();
        $attribute->addOption($option1);
        $attribute->addOption($option2);

        $filter->setAttribute($attribute);

        $expected = [
            [
                'title' => $option1->getValue(),
                'value' => $option1->getUid()
            ],
            [
                'title' => $option2->getValue(),
                'value' => $option2->getUid()
            ],
        ];


        $filter->setType(Filter::TYPE_ATTRIBUTES);

        $this->viewHelper->_set('arguments', ['filter' => $filter]);


        self::assertEquals(
            $expected,
            $this->viewHelper->render()
        );
    }
}

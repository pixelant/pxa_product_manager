<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Filter;
use Pixelant\PxaProductManager\Domain\Model\Option;

class FilterTest extends UnitTestCase
{
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Filter();
    }

    /**
     * @test
     */
    public function getOptionsReturnCategoriesOptions(): void
    {
        $this->subject->setType(Filter::TYPE_CATEGORIES);

        $cat1 = createEntity(Category::class, ['uid' => 11, 'title' => 'test']);
        $cat2 = createEntity(Category::class, ['uid' => 22, 'title' => 'second']);

        $categories = createObjectStorage($cat1, $cat2);

        $category = $this->prophesize(Category::class);
        $category->getSubCategories()->willReturn($categories);

        $this->subject->setCategory($category->reveal());

        $expect = [
            ['value' => $cat1->getUid(), 'label' => $cat1->getTitle()],
            ['value' => $cat2->getUid(), 'label' => $cat2->getTitle()],
        ];

        self::assertEquals($expect, $this->subject->getOptions());
    }

    /**
     * @test
     */
    public function getOptionsReturnAttributeOptions(): void
    {
        $this->subject->setType(Filter::TYPE_ATTRIBUTES);

        $opt1 = createEntity(Option::class, ['uid' => 101, 'value' => 'value1']);
        $opt2 = createEntity(Option::class, ['uid' => 102, 'value' => 'value2']);

        $options = createObjectStorage($opt1, $opt2);

        $attribute = $this->prophesize(Attribute::class);
        $attribute->getOptions()->willReturn($options);

        $this->subject->setAttribute($attribute->reveal());

        $expect = [
            ['value' => $opt1->getUid(), 'label' => $opt1->getValue()],
            ['value' => $opt2->getUid(), 'label' => $opt2->getValue()],
        ];

        self::assertEquals($expect, $this->subject->getOptions());
    }
}

<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Category;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model
 */
class CategoryTest extends UnitTestCase
{
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = new Category();
    }

    /**
     * @test
     */
    public function getParentsRootLineReturnRootLineOfParents()
    {
        $lastCategory = createCategoriesRootLineAndReturnLastCategory();

        $this->subject->setParent($lastCategory);
        $this->subject->_setProperty('uid', 100);

        // Vise versa
        $expect = [100, 5, 4, 3, 2, 1];
        $result = entitiesToUidsArray($this->subject->getParentsRootLine());

        // Compare  UIDs, because object won't be same
        $this->assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function getParentsRootLineReverseReturnReversedRootLine()
    {
        $lastCategory = createCategoriesRootLineAndReturnLastCategory();

        $this->subject->setParent($lastCategory);
        $this->subject->_setProperty('uid', 100);

        $expect = [1, 2, 3, 4, 5, 100];
        $result = entitiesToUidsArray($this->subject->getParentsRootLineReverse());

        // Compare  UIDs, because object won't be same
        $this->assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function getParentsRootLineReturnRootLineOfParentsButStopIfLoopFound()
    {
        $root = createEntity(Category::class, 99);
        $subCat1 = createEntity(Category::class, 1);
        $subCat2 = createEntity(Category::class, 2);
        $subCat3 = createEntity(Category::class, 3);

        $subCat3->setParent($subCat2);
        $subCat2->setParent($subCat1);
        $subCat1->setParent($root);

        // Here we have possible loop
        $root->setParent($subCat3);

        $this->subject->setParent($subCat3);
        $this->subject->_setProperty('uid', 100);

        $expect = [100, 3, 2, 1, 99];
        $result = array_map(fn($cat) => $cat->getUid(), $this->subject->getParentsRootLine());

        $this->assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function getNavigationRootLineReturnCategoriesForUrl()
    {
        $lastCategory = createCategoriesRootLineAndReturnLastCategory();
        $lastCategory->setHiddenInNavigation(true);

        $this->subject->setParent($lastCategory);
        $this->subject->_setProperty('uid', 100);

        // Current subject as last,
        // exclude last category from given rootline
        $expect = [
            1,
            2,
            3,
            4,
            $this->subject->getUid()
        ];

        $result = array_map(fn($cat) => $cat->getUid(), $this->subject->getNavigationRootLine());

        $this->assertEquals($expect, array_values($result));
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
        $this->subject->setTitle('title');

        $this->assertEquals('title', $this->subject->getNavigationTitle());
    }
}

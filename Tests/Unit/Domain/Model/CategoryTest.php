<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;

class CategoryTest extends UnitTestCase
{
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Category();
    }

    /**
     * @test
     */
    public function getParentsRootLineReturnRootLineOfParents(): void
    {
        $lastCategory = TestsUtility::createCategoriesRootLineAndReturnLastCategory();

        $this->subject->setParent($lastCategory);
        $this->subject->_setProperty('uid', 100);

        // Vise versa
        $expect = [100, 5, 4, 3, 2, 1];
        $result = TestsUtility::entitiesToUidsArray($this->subject->getParentsRootLine());

        // Compare  UIDs, because object won't be same
        self::assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function getParentsRootLineReverseReturnReversedRootLine(): void
    {
        $lastCategory = TestsUtility::createCategoriesRootLineAndReturnLastCategory();

        $this->subject->setParent($lastCategory);
        $this->subject->_setProperty('uid', 100);

        $expect = [1, 2, 3, 4, 5, 100];
        $result = TestsUtility::entitiesToUidsArray($this->subject->getParentsRootLineReverse());

        // Compare  UIDs, because object won't be same
        self::assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function getParentsRootLineReturnRootLineOfParentsButStopIfLoopFound(): void
    {
        $root = TestsUtility::createEntity(Category::class, 99);
        $subCat1 = TestsUtility::createEntity(Category::class, 1);
        $subCat2 = TestsUtility::createEntity(Category::class, 2);
        $subCat3 = TestsUtility::createEntity(Category::class, 3);

        $subCat3->setParent($subCat2);
        $subCat2->setParent($subCat1);
        $subCat1->setParent($root);

        // Here we have possible loop
        $root->setParent($subCat3);

        $this->subject->setParent($subCat3);
        $this->subject->_setProperty('uid', 100);

        $expect = [100, 3, 2, 1, 99];
        $result = array_map(fn ($cat) => $cat->getUid(), $this->subject->getParentsRootLine());

        self::assertEquals($expect, $result);
    }

    /**
     * @test
     */
    public function getNavigationRootLineReturnCategoriesForUrl(): void
    {
        $lastCategory = TestsUtility::createCategoriesRootLineAndReturnLastCategory();
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
            $this->subject->getUid(),
        ];

        $result = array_map(fn ($cat) => $cat->getUid(), $this->subject->getNavigationRootLine());

        self::assertEquals($expect, array_values($result));
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
        $this->subject->setTitle('title');

        self::assertEquals('title', $this->subject->getNavigationTitle());
    }
}

<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Domain\Repository;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class CategoryRepositoryTest
 * @package Pixelant\PxaProductManager\Tests\Functional\Domain\Repository
 */
class CategoryRepositoryTest extends FunctionalTestCase
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRespository;

    protected $testExtensionsToLoad = ['typo3conf/ext/pxa_product_manager'];

    protected function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../../Fixtures/sys_category.xml');
        $this->categoryRespository = GeneralUtility::makeInstance(ObjectManager::class)->get(CategoryRepository::class);
    }

    /**
     * @test
     */
    public function getChildrenCategoriesWillReturnChildrenCategoriesWithParentList()
    {
        $result = $this->categoryRespository->getChildrenCategories([1]);

        $this->assertCount(
            6, // categories in fixture
            $result
        );
    }

    /**
     * @test
     */
    public function getChildrenCategoriesWillReturnChildrenCategoriesWithoutParentList()
    {
        $result = $this->categoryRespository->getChildrenCategories([1], true);

        $this->assertCount(
            5, // categories in fixture
            $result
        );
    }
}

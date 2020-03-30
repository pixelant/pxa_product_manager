<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Domain\Model;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * @package Pixelant\PxaProductManager\Tests\Functional
 */
class CategoryTest extends FunctionalTestCase
{
    /**
     * @var object|CategoryRepository
     */
    protected $repository;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/pxa_product_manager'
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->repository = GeneralUtility::makeInstance(ObjectManager::class)->get(CategoryRepository::class);
    }

    /**
     * @test
     */
    public function mappingOfCustomFieldsIsWorking()
    {
        $this->importDataSet(__DIR__ . '/../../../Fixtures/categories_root_line_with_product.xml');

        $category = $this->repository->findByUid(10);
        $category->setHiddenInNavigation(true);
        $category->setMetaDescription('some_meta');
        $category->setContentPage(300);

        $this->repository->update($category);

        $persistanceManager = GeneralUtility::makeInstance(ObjectManager::class)->get(PersistenceManagerInterface::class);
        $persistanceManager->persistAll();
        $persistanceManager->clearState();

        /** @var Category $persistedCategory */
        $persistedCategory = $this->repository->findByUid($category->getUid());

        $this->assertTrue($persistedCategory->isHiddenInNavigation());
        $this->assertEquals($category->getMetaDescription(), $persistedCategory->getMetaDescription());
        $this->assertEquals($category->getContentPage(), $persistedCategory->getContentPage());
    }
}
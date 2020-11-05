<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Domain\Model;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ProductTest extends FunctionalTestCase
{
    /**
     * @var object|ProductRepository
     */
    protected $repository;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/pxa_product_manager',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = GeneralUtility::makeInstance(ObjectManager::class)->get(ProductRepository::class);
    }

    /**
     * @test
     */
    public function getAllAttributesSetsReturnAttributesSetsOfCategoriesAndProductInCorrectOrder(): void
    {
        $this->importDataSet(__DIR__ . '/../../../Fixtures/products_attributes_set_test.xml');

        $product = $this->repository->findByUid(1000);

        $allAttributesSets = $product->_getAllAttributesSets();

        $expectAttributesSetsUids = [3, 1];
        self::assertEquals($expectAttributesSetsUids, TestsUtility::entitiesToUidsArray($allAttributesSets));
    }

    /**
     * @test
     */
    public function getCategoriesWithParentsReturnAllRootLineExcludeDuplications(): void
    {
        $this->importDataSet(__DIR__ . '/../../../Fixtures/categories_root_line_with_product.xml');
        $product = $this->repository->findByUid(10);

        $expect = [20, 10, 40, 30, 50];

        self::assertEquals($expect, TestsUtility::entitiesToUidsArray($product->getCategoriesWithParents()));
    }
}

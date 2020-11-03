<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Domain\Model;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Domain\Repository\AttributeSetRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package Pixelant\PxaProductManager\Tests\Functional
 */
class ProductTest extends FunctionalTestCase
{
    /**
     * @var object|ProductRepository
     */
    protected $repository;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/pxa_product_manager'
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->repository = GeneralUtility::makeInstance(ObjectManager::class)->get(ProductRepository::class);
    }

    /**
     * @test
     */
    public function getAllAttributesSetsReturnAttributesSetsOfCategoriesAndProductInCorrectOrder()
    {
        $this->importDataSet(__DIR__ . '/../../../Fixtures/products_attributes_set_test.xml');

        $product = $this->repository->findByUid(1000);

        $allAttributesSets = $product->_getAllAttributesSets();

        $expectAttributesSetsUids = [3, 1];
        $this->assertEquals($expectAttributesSetsUids, entitiesToUidsArray($allAttributesSets));
    }

    /**
     * @test
     */
    public function getCategoriesWithParentsReturnAllRootLineExcludeDuplications()
    {
        $this->importDataSet(__DIR__ . '/../../../Fixtures/categories_root_line_with_product.xml');
        $product = $this->repository->findByUid(10);

        $expect = [20, 10, 40, 30, 50];

        $this->assertEquals($expect, entitiesToUidsArray($product->getCategoriesWithParents()));
    }
}

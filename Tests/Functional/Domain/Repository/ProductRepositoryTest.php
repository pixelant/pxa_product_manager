<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Domain\Repository;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ProductRepositoryTest.php
 * @package Pixelant\PxaProductManager\Tests\Functional\Domain\Repository
 */
class ProductRepositoryTest extends FunctionalTestCase
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    protected $testExtensionsToLoad = ['typo3conf/ext/pxa_product_manager'];

    protected function setUp()
    {
        parent::setUp();
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_pxaproductmanager_domain_model_product.xml');
        $this->productRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(ProductRepository::class);
    }

    /**
     * @test
     */
    public function findByUidHiddenRecordWhenHiddenAllowedReturnProduct()
    {
        $uid = 202;

        $product = $this->productRepository->findByUid($uid, false);

        $this->assertNotNull($product);
        $this->assertEquals($uid, $product->getUid());
    }

    /**
     * @test
     */
    public function findByUidHiddenRecordWhenHiddenDisallowedReturnNull()
    {
        $uid = 202;

        $product = $this->productRepository->findByUid($uid);

        $this->assertNull($product);
    }

    /**
     * @test
     */
    public function findByUidVisibleRecordReturnProduct()
    {
        $uid = 1;

        $product = $this->productRepository->findByUid($uid);

        $this->assertNotNull($product);
        $this->assertEquals($uid, $product->getUid());
    }

    /**
     * @test
     */
    public function findByUidHiddenDeletedRecordWhenHiddenAllowedReturnNull()
    {
        $uid = 203;

        $product = $this->productRepository->findByUid($uid, false);

        $this->assertNull($product);
    }
}

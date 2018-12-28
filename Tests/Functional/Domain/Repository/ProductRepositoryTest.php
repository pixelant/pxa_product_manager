<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Domain\Repository;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic\Query;
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
        $this->importDataSet(__DIR__ . '/../../Fixtures/tx_pxaproductmanager_domain_model_option.xml');
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

    /**
     * @test
     */
    public function createQueryReturnInstanceOfOwnQuery()
    {
        $query = $this->productRepository->createQuery();

        $this->assertInstanceOf(
            Query::class,
            $query
        );
    }

    /**
     * @test
     */
    public function getProductByMatchAttributeValueUsingQueryParserReturnProduct()
    {
        $query = $this->productRepository->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        $query->matching(
            $query->equals(
                'attributesValues->1',
                '200'
            )
        );

        $sql = $this->productRepository->convertQueryBuilderToSql($query);

        $product = $query->statement($sql)->execute();

        $this->assertEquals(100, $product->getFirst()->getUid());
    }

    /**
     * @test
     */
    public function getProductByContainAttributeValueUsingQueryParserReturnProduct()
    {
        $query = $this->productRepository->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        $query->matching(
            $query->contains(
                'attributesValues->3',
                '2'
            )
        );

        $sql = $this->productRepository->convertQueryBuilderToSql($query);

        $product = $query->statement($sql)->execute();

        $this->assertEquals(1, $product->getFirst()->getUid());
    }

    /**
     * @test
     */
    public function getProductByLikeAttributeValueUsingQueryParserReturnProduct()
    {
        $query = $this->productRepository->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        $query->matching(
            $query->like(
                'attributesValues->1',
                'test value'
            )
        );

        $sql = $this->productRepository->convertQueryBuilderToSql($query);

        $product = $query->statement($sql)->execute();

        $this->assertEquals(1, $product->getFirst()->getUid());
    }

    /**
     * @test
     */
    public function getProductByInAttributeValueUsingQueryParserReturnProduct()
    {
        $query = $this->productRepository->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        $query->matching(
            $query->in(
                'attributesValues->2',
                [
                    2,
                    1,
                    3
                ]
            )
        );

        $sql = $this->productRepository->convertQueryBuilderToSql($query);

        $product = $query->statement($sql)->execute();

        $this->assertEquals(100, $product->getFirst()->getUid());
    }

    /**
     * @test
     */
    public function getProductByRangeAttributeValueUsingQueryParserReturnProduct()
    {
        $query = $this->productRepository->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        // Find where options value between given range
        $query->matching(
            $query->attributesRange(
                'attributesValues->3',
                190,
                200
            )
        );

        $sql = $this->productRepository->convertQueryBuilderToSql($query);

        $product = $query->statement($sql)->execute();

        $this->assertEquals(1, $product->getFirst()->getUid());
    }
}

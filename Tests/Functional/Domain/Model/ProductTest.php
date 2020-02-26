<?php

namespace Pixelant\PxaProductManager\Tests\Functional;

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package Pixelant\PxaProductManager\Tests\Functional
 */
class ProductTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/pxa_product_manager'
    ];

    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function test()
    {
        $db = $this->getDatabaseConnection();

        $db->insertArray('tx_pxaproductmanager_domain_model_product', ['name' => 'ahahah']);

        $product = (new ObjectManager())->get(ProductRepository::class)->findByName('ahahah')->getFirst();

        $this->assertNotNull($product);
        $this->assertEquals('ahahah', $product->getName());
    }
}

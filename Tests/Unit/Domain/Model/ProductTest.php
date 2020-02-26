<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model
 */
class ProductTest extends UnitTestCase
{
    /**
     * @var Product
     */
    protected $product;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->product = new Product();
    }

    /**
     * @test
     */
    public function getAttributeSets()
    {
        
    }
}

<?php
declare(strict_types=1);
namespace Pixelant\PxaProductManager\Tests\Unit\Service;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Service\Resource\ResourceConverter;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Service
 */
class ResourceConverterTest extends UnitTestCase
{
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = new ResourceConverter();
    }

    /**
     * @test
     */
    public function translateEntityNameToResourceNameReturnCorrectResourceName()
    {
        $expect = 'Pixelant\PxaProductManager\Domain\Resource\Product';

        $this->assertEquals($expect, $this->callInaccessibleMethod($this->subject, 'translateEntityNameToResourceName', createEntity(Product::class, 1)));
    }
}

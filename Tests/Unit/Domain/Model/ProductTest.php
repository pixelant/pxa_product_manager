<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Service\AttributesValuesMapper;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model
 */
class ProductTest extends UnitTestCase
{
    /**
     * @var Product
     */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = new Product();
    }

    /**
     * @test
     */
    public function getAttributesWillFillValues()
    {
        $am = $this->prophesize(AttributesValuesMapper::class);
        $am->map($this->subject)->shouldBeCalled();

        $this->subject->injectAttributesValuesMapper($am->reveal());

        $this->subject->getAttributes();
    }
}

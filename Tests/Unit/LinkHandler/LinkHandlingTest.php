<?php

namespace Pixelant\PxaProductManager\Tests\Unit\LinkHandler;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\LinkHandler\LinkHandling;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class LinkHandlingTest
 * @package Pixelant\PxaProductManager\Tests\Unit\LinkHandler
 */
class LinkHandlingTest extends UnitTestCase
{
    /**
     * @var LinkHandling
     */
    protected $linkHandling;

    protected function setUp()
    {
        $this->linkHandling = GeneralUtility::makeInstance(LinkHandling::class);
    }

    /**
     * @test
     */
    public function asStringWithProductReturnUrlForProduct()
    {
        $parameters = ['product' => 123];

        $this->assertEquals(
            't3://pxappm?product=123',
            $this->linkHandling->asString($parameters)
        );
    }

    /**
     * @test
     */
    public function asStringWithCategoryReturnUrlForCategory()
    {
        $parameters = ['category' => 123];

        $this->assertEquals(
            't3://pxappm?category=123',
            $this->linkHandling->asString($parameters)
        );
    }

    /**
     * @test
     */
    public function resolveHandlerDataWithNoDataReturnEmptyArray()
    {
        $this->assertEmpty(
            $this->linkHandling->resolveHandlerData([])
        );
    }

    /**
     * @test
     */
    public function resolveHandlerForProductReturnDataWithProduct()
    {
        $data['product'] = 123;

        $this->assertEquals(
            $data,
            $this->linkHandling->resolveHandlerData($data)
        );
    }

    /**
     * @test
     */
    public function resolveHandlerForCategoryReturnDataWithCategory()
    {
        $data['category'] = 123;

        $this->assertEquals(
            $data,
            $this->linkHandling->resolveHandlerData($data)
        );
    }
}

<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Formatter;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Formatter\PriceFormatter;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Http\ServerRequest;

class PriceFormatterTest extends UnitTestCase
{
    /**
     * Test number of fraction digits.
     */
    public function testPriceFormatterReturnsCorrectNumberOfFractionDigits(): void
    {
        $eventDispatcherProphecy = $this->prophesize(EventDispatcher::class);
        $requestProphecy = $this->prophesize(ServerRequest::class);

        $priceFormatter = new PriceFormatter($requestProphecy->reveal());
        $priceFormatter->injectDispatcher($eventDispatcherProphecy->reveal());

        $product = TestsUtility::createEntity(
            Product::class,
            [
                'uid' => 1,
                'price' => 123456.78,
            ]
        );

        self::assertEquals('$123,456.78', $priceFormatter->format($product, '', 'USD', 2));
        self::assertEquals('$123,456.8', $priceFormatter->format($product, '', 'USD', 1));
        self::assertEquals('$123,457', $priceFormatter->format($product, '', 'USD', 0));
    }
}

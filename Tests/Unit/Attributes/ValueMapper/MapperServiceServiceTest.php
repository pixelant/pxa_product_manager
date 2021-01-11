<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueMapper;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueMapper\MapperService;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;

class MapperServiceServiceTest extends UnitTestCase
{
    /**
     * @test
     */
    public function mapReturnAttributesSetsAfterProcessing(): void
    {
        $service = $this->createPartialMock(MapperService::class, ['process']);
        $service->expects(self::once())->method('process');

        $sets = TestsUtility::createMultipleEntities(AttributeSet::class, 3);

        $product = $this->createMock(Product::class);
        $product->expects(self::once())->method('getAttributesValuesWithValidAttributes')->willReturn($sets);

        self::assertEquals($sets, $service->map($product));
    }
}

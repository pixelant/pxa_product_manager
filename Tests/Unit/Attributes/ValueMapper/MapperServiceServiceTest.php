<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueMapper;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueMapper\MapperService;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\Product;

class MapperServiceServiceTest extends UnitTestCase
{
    /**
     * @test
     */
    public function mapReturnAttributesSetsAfterProcessing(): void
    {
        $service = $this->createPartialMock(MapperService::class, ['process']);
        $service->expects(self::once())->method('process');

        $sets = createMultipleEntities(AttributeSet::class, 3);

        $product = $this->createMock(Product::class);
        $product->expects(self::once())->method('_getAllAttributesSets')->willReturn($sets);

        self::assertEquals($sets, $service->map($product));
    }
}

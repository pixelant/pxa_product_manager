<?php
declare(strict_types=1);
namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueMapper;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueMapper\MapperService;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Adapter\Attributes\ValueMapper
 */
class MapperServiceServiceTest extends UnitTestCase
{
    /**
     * @test
     */
    public function mapReturnAttributesSetsAfterProcessing()
    {
        $service = $this->createPartialMock(MapperService::class, ['process']);
        $service->expects($this->once())->method('process');

        $sets = createMultipleEntities(AttributeSet::class, 3);

        $product = $this->createMock(Product::class);
        $product->expects($this->once())->method('_getAllAttributesSets')->willReturn($sets);

        $this->assertEquals($sets, $service->map($product));
    }
}

<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Service;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Service\Resource\ResourceConverter;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;

class ResourceConverterTest extends UnitTestCase
{
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new ResourceConverter();
    }

    /**
     * @test
     */
    public function translateEntityNameToResourceNameReturnCorrectResourceName(): void
    {
        $expect = 'Pixelant\PxaProductManager\Domain\Resource\Product';

        self::assertEquals(
            $expect,
            $this->callInaccessibleMethod(
                $this->subject,
                'translateEntityNameToResourceName',
                TestsUtility::createEntity(Product::class, 1)
            )
        );
    }
}

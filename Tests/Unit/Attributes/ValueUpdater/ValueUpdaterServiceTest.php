<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueUpdater;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueUpdater\ValueUpdaterService;
use Pixelant\PxaProductManager\Domain\Model\Product;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueUpdater
 */
class ValueUpdaterServiceTest extends UnitTestCase
{
    protected $subject;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new ValueUpdaterService();
    }

    /**
     * @test
     */
    public function castToIntConvertObjectToUid()
    {
        $object = createEntity(Product::class, ['_localizedUid' => 12]);

        $this->assertEquals(12, $this->callInaccessibleMethod($this->subject, 'castToInt', $object));
    }

    /**
     * @test
     */
    public function castToIntReturnUidIfIntGiven()
    {
        $this->assertEquals(10, $this->callInaccessibleMethod($this->subject, 'castToInt', 10));
    }
}

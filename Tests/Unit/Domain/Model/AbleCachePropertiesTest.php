<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\AbleCacheProperties;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model
 */
class AbleCachePropertiesTest extends UnitTestCase
{
    use AbleCacheProperties;

    protected function setUp()
    {
        parent::setUp();

        $this->cacheProperties = [];
    }

    /**
     * @test
     */
    public function getCachedPropertyReturnValueOfPropertyFromCache()
    {
        $this->cacheProperties['test'] = 'value';

        $this->assertEquals('value', $this->getCachedProperty('test', function () {}));
    }

    /**
     * @test
     */
    public function getCachedPropertyReturnValueOfPropertyProvidedByClosure()
    {
        $value = 'test value';
        $key = 'testkey';

        $this->assertEquals($value, $this->getCachedProperty($key, fn() => $value));
        $this->assertEquals($this->cacheProperties[$key], $value);
    }
}

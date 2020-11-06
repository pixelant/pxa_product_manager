<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\CanCacheProperties;

class CanCachePropertiesTest extends UnitTestCase
{
    use CanCacheProperties;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheProperties = [];
    }

    /**
     * @test
     */
    public function getCachedPropertyReturnValueOfPropertyFromCache(): void
    {
        $this->cacheProperties['test'] = 'value';
        // @codingStandardsIgnoreStart
        self::assertEquals('value', $this->getCachedProperty('test', function (): void {}));
        // @codingStandardsIgnoreEnd
    }

    /**
     * @test
     */
    public function getCachedPropertyReturnValueOfPropertyProvidedByClosure(): void
    {
        $value = 'test value';
        $key = 'testkey';

        self::assertEquals($value, $this->getCachedProperty($key, fn () => $value));
        self::assertEquals($this->cacheProperties[$key], $value);
    }
}

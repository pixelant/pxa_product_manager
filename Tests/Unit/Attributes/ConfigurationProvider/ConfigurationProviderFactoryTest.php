<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ConfigurationProvider;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ConfigurationProvider\ConfigurationProviderFactory;

class ConfigurationProviderFactoryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createThrownExceptionIfTypeOfAttributeIsNotSupported(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        ConfigurationProviderFactory::create(
            1,
            [
                'type' => 'VERY-MUCH-UNSUPPORTED-TYPE',
            ]
        );
    }
}

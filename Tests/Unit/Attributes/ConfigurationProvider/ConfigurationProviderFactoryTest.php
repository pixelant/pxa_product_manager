<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ConfigurationProvider;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ConfigurationProvider\ConfigurationProviderFactory;
use Pixelant\PxaProductManager\Domain\Model\Attribute;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit
 */
class ConfigurationProviderFactoryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createThrownExceptionIfTypeOfAttributeIsNotSupported()
    {
        $attribute = createEntity(Attribute::class, 1);

        $this->expectException(\UnexpectedValueException::class);
        ConfigurationProviderFactory::create($attribute);
    }
}

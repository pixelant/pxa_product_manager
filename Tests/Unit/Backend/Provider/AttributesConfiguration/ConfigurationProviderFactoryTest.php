<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Backend\Provider\AttributesConfiguration;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Backend\Provider\AttributesConfiguration\ConfigurationProviderFactory;
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

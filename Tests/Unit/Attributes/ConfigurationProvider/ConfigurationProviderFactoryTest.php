<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ConfigurationProvider;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ConfigurationProvider\ConfigurationProviderFactory;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;

class ConfigurationProviderFactoryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createThrownExceptionIfTypeOfAttributeIsNotSupported(): void
    {
        $attribute = TestsUtility::createEntity(Attribute::class, 1);

        $this->expectException(\UnexpectedValueException::class);
        ConfigurationProviderFactory::create($attribute);
    }
}

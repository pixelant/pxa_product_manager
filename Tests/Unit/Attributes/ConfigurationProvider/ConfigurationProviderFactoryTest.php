<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ConfigurationProvider;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ConfigurationProvider\ConfigurationProviderFactory;
use Pixelant\PxaProductManager\Domain\Model\Attribute;

class ConfigurationProviderFactoryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createThrownExceptionIfTypeOfAttributeIsNotSupported(): void
    {
        $attribute = createEntity(Attribute::class, 1);

        $this->expectException(\UnexpectedValueException::class);
        ConfigurationProviderFactory::create($attribute);
    }
}

<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ConfigurationProvider;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ConfigurationProvider\AbstractProvider;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;

class AbstractProviderTest extends UnitTestCase
{
    /**
     * @var AbstractProvider
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getMockBuilder(AbstractProvider::class)
            ->disableOriginalConstructor()
            ->setMethods(['overrideWithSpecificTca'])
            ->getMock();
    }

    /**
     * @test
     */
    public function getAttributeConfigurationReturnConfigurationByAttributeTypeAndSetNameAsLabel(): void
    {
        $type = Attribute::ATTRIBUTE_TYPE_INPUT;
        $testConf = ['conf' => ['type' => 'input']];

        $attribute = ['uid' => 1, 'type' => $type, 'name' => 'Attribute'];

        $tca[$type] = $testConf;

        $this->inject($this->subject, 'attribute', $attribute);
        $this->inject($this->subject, 'tca', $tca);

        self::assertEquals(
            $testConf + ['label' => 'Attribute'],
            $this->callInaccessibleMethod($this->subject, 'getAttributeConfiguration')
        );
    }

    /**
     * @test
     */
    public function isRequiredReturnTrueIfAttributeIsRequired(): void
    {
        $attribute = ['uid' => 1, 'required' => true];

        $this->inject($this->subject, 'attribute', $attribute);

        self::assertTrue($this->callInaccessibleMethod($this->subject, 'isRequired'));
    }
}

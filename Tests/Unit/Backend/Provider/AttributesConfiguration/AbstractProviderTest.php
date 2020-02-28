<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Backend\Provider\AttributesConfiguration;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Backend\Provider\AttributesConfiguration\AbstractProvider;
use Pixelant\PxaProductManager\Domain\Model\Attribute;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Backend\Provider\AttributesConfiguration
 */
class AbstractProviderTest extends UnitTestCase
{
    /**
     * @var AbstractProvider
     */
    protected $subject;

    protected function setUp()
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
    public function getAttributeConfigurationReturnConfigurationByAttributeTypeAndSetNameAsLabel()
    {
        $type = Attribute::ATTRIBUTE_TYPE_INPUT;
        $testConf = ['conf' => ['type' => 'input']];

        $attribute = createEntity(Attribute::class, ['uid' => 1, 'type' => $type, 'name' => 'Attribute']);

        $tca[$type] = $testConf;

        $this->inject($this->subject, 'attribute', $attribute);
        $this->inject($this->subject, 'tca', $tca);

        $this->assertEquals(
            $testConf + ['label' => 'Attribute'],
            $this->callInaccessibleMethod($this->subject, 'getAttributeConfiguration'),
            );
    }

    /**
     * @test
     */
    public function isRequiredReturnTrueIfAttributeIsRequired()
    {
        $attribute = createEntity(Attribute::class, ['uid' => 1, 'required' => true]);

        $this->inject($this->subject, 'attribute', $attribute);

        $this->assertTrue($this->callInaccessibleMethod($this->subject, 'isRequired'));
    }
}

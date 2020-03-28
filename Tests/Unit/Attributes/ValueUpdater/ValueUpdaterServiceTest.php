<?php
declare(strict_types=1);
namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueUpdater;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueUpdater\ValueUpdaterService;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\AttributeRepository;

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

    /**
     * @test
     */
    public function getAttributeEntityTryToFindAttributeIfNotEntity()
    {
        $attribute = 10;
        $attributeEntity = createEntity(Attribute::class, 20);

        $repository = $this->prophesize(AttributeRepository::class);
        $repository->findByUid($attribute)->shouldBeCalled()->willReturn($attributeEntity);

        $this->subject->injectAttributeRepository($repository->reveal());

        $this->assertSame($attributeEntity, $this->callInaccessibleMethod($this->subject, 'getAttributeEntity', $attribute));
    }

    /**
     * @test
     * @dataProvider selectBoxTypes
     */
    public function convertValueReturnCommaWrappedValueForMultipleSelectBox($value, $expect, $type)
    {
        $attribute = createEntity(Attribute::class, ['uid' => 1, 'type' => $type]);

        $subject = $this->getMockBuilder(ValueUpdaterService::class)->setMethods(['getAttributeEntity'])->getMock();
        $subject->expects($this->once())->method('getAttributeEntity')->willReturn($attribute);

        $this->assertEquals($expect, $this->callInaccessibleMethod($subject, 'convertValue', $attribute, $value));
    }

    public function selectBoxTypes()
    {
        return [
            'single_select_box' => [
                '101',
                ',101,',
                Attribute::ATTRIBUTE_TYPE_DROPDOWN
            ],
            'multiple_select_box' => [
                '9,10,11',
                ',9,10,11,',
                Attribute::ATTRIBUTE_TYPE_MULTISELECT,
            ]
        ];
    }
}

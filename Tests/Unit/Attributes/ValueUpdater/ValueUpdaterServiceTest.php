<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Attributes\ValueUpdater;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Attributes\ValueUpdater\ValueUpdaterService;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\AttributeRepository;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;

class ValueUpdaterServiceTest extends UnitTestCase
{
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new ValueUpdaterService();
    }

    /**
     * @test
     */
    public function castToIntConvertObjectToUid(): void
    {
        $object = TestsUtility::createEntity(Product::class, ['_localizedUid' => 12]);

        self::assertEquals(12, $this->callInaccessibleMethod($this->subject, 'castToInt', $object));
    }

    /**
     * @test
     */
    public function castToIntReturnUidIfIntGiven(): void
    {
        self::assertEquals(10, $this->callInaccessibleMethod($this->subject, 'castToInt', 10));
    }

    /**
     * @test
     */
    public function getAttributeEntityTryToFindAttributeIfNotEntity(): void
    {
        $attribute = 10;
        $attributeEntity = TestsUtility::createEntity(Attribute::class, 20);

        $repository = $this->prophesize(AttributeRepository::class);
        $repository->findByUid($attribute)->shouldBeCalled()->willReturn($attributeEntity);

        $this->subject->injectAttributeRepository($repository->reveal());

        self::assertSame(
            $attributeEntity,
            $this->callInaccessibleMethod(
                $this->subject,
                'getAttributeEntity',
                $attribute
            )
        );
    }

    /**
     * @test
     * @dataProvider selectBoxTypes
     * @param mixed $value
     * @param mixed $expect
     * @param mixed $type
     */
    public function convertValueReturnCommaWrappedValueForMultipleSelectBox($value, $expect, $type): void
    {
        $attribute = TestsUtility::createEntity(Attribute::class, ['uid' => 1, 'type' => $type]);

        $subject = $this->getMockBuilder(ValueUpdaterService::class)->setMethods(['getAttributeEntity'])->getMock();
        $subject->expects(self::once())->method('getAttributeEntity')->willReturn($attribute);

        self::assertEquals($expect, $this->callInaccessibleMethod($subject, 'convertValue', $attribute, $value));
    }

    public function selectBoxTypes()
    {
        return [
            'single_select_box' => [
                '101',
                ',101,',
                Attribute::ATTRIBUTE_TYPE_DROPDOWN,
            ],
            'multiple_select_box' => [
                '9,10,11',
                ',9,10,11,',
                Attribute::ATTRIBUTE_TYPE_MULTISELECT,
            ],
        ];
    }
}

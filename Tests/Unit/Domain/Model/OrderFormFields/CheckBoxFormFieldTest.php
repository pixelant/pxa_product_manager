<?php


namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model\OrderFormFields;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Pixelant\PxaProductManager\Domain\Model\OrderFormField;
use Pixelant\PxaProductManager\Domain\Model\OrderFormFields\CheckBoxFormField;

/**
 * Class CheckBoxFormFieldTest
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model\OrderFormFields
 */
class CheckBoxFormFieldTest extends UnitTestCase
{
    /**
     * @var CheckBoxFormField|MockObject
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = $this
            ->getMockBuilder(CheckBoxFormField::class)
            ->setMethods(['translateKey'])
            ->getMock();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function getValueAsTextForCheckBoxTryToTranslateValue()
    {
        $expectKey = 'fe.checkbox.1';
        $value = '1';

        $this->fixture->setValue($value);
        $this->fixture->setType(OrderFormField::FIELD_CHECKBOX);

        $this->fixture
            ->expects($this->once())
            ->method('translateKey')
            ->with($expectKey);

        $this->fixture->getValueAsText();
    }
}

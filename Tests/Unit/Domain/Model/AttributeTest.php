<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model
 */
class AttributeTest extends UnitTestCase
{
    protected $subject;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new Attribute();
    }

    /**
     * @test
     * @dataProvider isFalTypeProvider
     */
    public function isFalTypeCanCheckIfTypeOfAttributeIsImageOrFile($type, $expect)
    {
        $this->subject->setType($type);

        $this->assertEquals($expect, $this->subject->isFalType());
    }

    /**
     * @test
     */
    public function isInputTypeReturnTrueOnInput()
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_INPUT);

        $this->assertTrue($this->subject->isInputType());
    }

    /**
     * @test
     */
    public function isTextAreaReturnTrueOnTextArea()
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_TEXT);

        $this->assertTrue($this->subject->isTextArea());
    }

    /**
     * @test
     */
    public function isTextAreaReturnTrueOnSelectBox()
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_DROPDOWN);

        $this->assertTrue($this->subject->isSelectBoxType());
    }

    /**
     * @test
     */
    public function isInputTypeReturnTrueOnMultipleSelectBox()
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_MULTISELECT);

        $this->assertTrue($this->subject->isSelectBoxType());
    }

    /**
     * @test
     */
    public function isDateTypeReturnTrueOnDateType()
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_DATETIME);

        $this->assertTrue($this->subject->isDateType());
    }

    /**
     * @test
     */
    public function isCheckboxTypeReturnTrueOnCheckbox()
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_CHECKBOX);

        $this->assertTrue($this->subject->isCheckboxType());
    }

    /**
     * @test
     */
    public function isLinkTypeReturnTrueOnLinkType()
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_LINK);

        $this->assertTrue($this->subject->isLinkType());
    }

    /**
     * @test
     */
    public function usingAsStringReturnValue()
    {
        $this->subject->setValue('value');

        $this->assertEquals('value', (string)$this->subject);
    }

    public function isFalTypeProvider()
    {
        return [
            'valid_image' => [
                'type' => Attribute::ATTRIBUTE_TYPE_IMAGE,
                'expect' => true,
            ],
            'valid_file' => [
                'type' => Attribute::ATTRIBUTE_TYPE_FILE,
                'expect' => true,
            ],
            'invalid_type' => [
                'type' => Attribute::ATTRIBUTE_TYPE_MULTISELECT,
                'expect' => false,
            ],
        ];
    }
}

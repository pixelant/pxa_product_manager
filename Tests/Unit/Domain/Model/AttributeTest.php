<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Attribute;

class AttributeTest extends UnitTestCase
{
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Attribute();
    }

    /**
     * @test
     */
    public function isMultipleSelectBoxReturnTrueonMultipleSelectBox(): void
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_MULTISELECT);

        self::assertTrue($this->subject->isMultipleSelectBox());
    }

    /**
     * @test
     * @dataProvider isFalTypeProvider
     * @param mixed $type
     * @param mixed $expect
     */
    public function isFalTypeCanCheckIfTypeOfAttributeIsImageOrFile($type, $expect): void
    {
        $this->subject->setType($type);

        self::assertEquals($expect, $this->subject->isFalType());
    }

    /**
     * @test
     */
    public function isInputTypeReturnTrueOnInput(): void
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_INPUT);

        self::assertTrue($this->subject->isInputType());
    }

    /**
     * @test
     */
    public function isTextAreaReturnTrueOnTextArea(): void
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_TEXT);

        self::assertTrue($this->subject->isTextArea());
    }

    /**
     * @test
     */
    public function isTextAreaReturnTrueOnSelectBox(): void
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_DROPDOWN);

        self::assertTrue($this->subject->isSelectBoxType());
    }

    /**
     * @test
     */
    public function isInputTypeReturnTrueOnMultipleSelectBox(): void
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_MULTISELECT);

        self::assertTrue($this->subject->isSelectBoxType());
    }

    /**
     * @test
     */
    public function isDateTypeReturnTrueOnDateType(): void
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_DATETIME);

        self::assertTrue($this->subject->isDateType());
    }

    /**
     * @test
     */
    public function isCheckboxTypeReturnTrueOnCheckbox(): void
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_CHECKBOX);

        self::assertTrue($this->subject->isCheckboxType());
    }

    /**
     * @test
     */
    public function isLinkTypeReturnTrueOnLinkType(): void
    {
        $this->subject->setType(Attribute::ATTRIBUTE_TYPE_LINK);

        self::assertTrue($this->subject->isLinkType());
    }

    /**
     * @test
     */
    public function usingAsStringReturnValue(): void
    {
        $this->subject->setStringValue('value');

        self::assertEquals('value', (string)$this->subject->getStringValue());
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

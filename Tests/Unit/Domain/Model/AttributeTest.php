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

<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Validation\Validator;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Validation\Validator\EmailValidator;
use Pixelant\PxaProductManager\Validation\Validator\RequiredValidator;
use Pixelant\PxaProductManager\Validation\Validator\UrlValidator;

/**
 * Class RequiredValidatorTest
 * @package Pixelant\PxaProductManager\Tests\Unit\Validation\Validator
 */
class RequiredValidatorTest extends UnitTestCase
{
    /**
     * @var RequiredValidator
     */
    protected $fixture = null;

    public function setUp()
    {
        $this->fixture = new RequiredValidator();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function validationOfNotEmptyReturnTrue()
    {
        $value = 'test';

        $this->assertTrue($this->fixture->validate($value));
    }

    /**
     * @test
     */
    public function validationOfEmptyStringReturnFalse()
    {
        $value = '';

        $this->assertFalse($this->fixture->validate($value));
    }

    /**
     * @test
     */
    public function validationOfZeroStringReturnFalse()
    {
        $value = '0';

        $this->assertFalse($this->fixture->validate($value));
    }

    /**
     * @test
     */
    public function validationOfZeroIntReturnFalse()
    {
        $value = 0;

        $this->assertFalse($this->fixture->validate($value));
    }

    /**
     * @test
     */
    public function validationOfNullReturnFalse()
    {
        $this->assertFalse($this->fixture->validate(null));
    }
}

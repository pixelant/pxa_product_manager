<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Validation\Validator;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Validation\Validator\EmailValidator;

/**
 * Class EmailValidatorTest
 * @package Pixelant\PxaProductManager\Tests\Unit\Validation\Validator
 */
class EmailValidatorTest extends UnitTestCase
{
    /**
     * @var EmailValidator
     */
    protected $fixture = null;

    public function setUp()
    {
        $this->fixture = new EmailValidator();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function validationOfValidEmailReturnTrue()
    {
        $email = 'andriy@pixelant.se';

        $this->assertTrue($this->fixture->validate($email));
    }

    /**
     * @test
     */
    public function validationOfInvalidEmailReturnFalse()
    {
        $email = 'andriypixelantse';

        $this->assertFalse($this->fixture->validate($email));
    }

    /**
     * Need to use required validation for such values
     *
     * @test
     */
    public function emptyStringIsValid()
    {
        $email = '';

        $this->assertTrue($this->fixture->validate($email));
    }
}

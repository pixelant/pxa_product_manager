<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Validation\Validator;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Validation\Validator\EmailValidator;
use Pixelant\PxaProductManager\Validation\Validator\UrlValidator;

/**
 * Class UrlValidatorTest
 * @package Pixelant\PxaProductManager\Tests\Unit\Validation\Validator
 */
class UrlValidatorTest extends UnitTestCase
{
    /**
     * @var UrlValidator
     */
    protected $fixture = null;

    public function setUp()
    {
        $this->fixture = new UrlValidator();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function validationOfValidUrlReturnTrue()
    {
        $url = 'https://site.com/';

        $this->assertTrue($this->fixture->validate($url));
    }

    /**
     * @test
     */
    public function validationOfInvalidUrlReturnFalse()
    {
        $url = '//site.com/test';

        $this->assertFalse($this->fixture->validate($url));
    }
}

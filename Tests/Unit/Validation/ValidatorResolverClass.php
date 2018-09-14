<?php

namespace Pixelant\PxaProductManager\Tests\Validation;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Exception\Validation\NoSuchValidatorException;
use Pixelant\PxaProductManager\Validation\ValidatorResolver;

/**
 * Class ValidatorResolverClass
 * @package Pixelant\PxaProductManager\Tests\Validation
 */
class ValidatorResolverClass extends UnitTestCase
{
    /**
     * @var ValidatorResolver
     */
    protected $fixture = null;

    public function setUp()
    {
        $this->fixture = new ValidatorResolver();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function registerValidatorNewValidatorWillRegisterNewValidator()
    {
        $reflectionClass = new \ReflectionClass(ValidatorResolver::class);

        $reflectionProperty = $reflectionClass->getProperty('validatorsRegistry');
        $reflectionProperty->setAccessible(true);

        $defaultValidators = $reflectionProperty->getValue();
        $expect = $defaultValidators + ['test' => 'SomeTestClass::class'];

        ValidatorResolver::registerValidator(
            'test',
            'SomeTestClass::class'
        );

        $this->assertEquals($expect, $reflectionProperty->getValue());
    }

    /**
     * @test
     * @throws NoSuchValidatorException
     */
    public function createValidatorOnNonExistingValidatorThrownException()
    {
        $this->expectException(NoSuchValidatorException::class);

        $this->fixture->createValidator('test');
    }
}

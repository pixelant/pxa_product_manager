<?php


namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Option;
use Pixelant\PxaProductManager\Domain\Model\OrderFormField;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class OrderFormFieldTest
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model
 */
class OrderFormFieldTest extends UnitTestCase
{
    /**
     * @var OrderFormField
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new OrderFormField();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function defaultNameIsEmpty()
    {
        $this->assertEmpty($this->fixture->getName());
    }

    /**
     * @test
     */
    public function nameCanBeSet()
    {
        $value = 'test';

        $this->fixture->setName($value);
        $this->assertEquals($value, $this->fixture->getName());
    }

    /**
     * @test
     */
    public function defaultLabelIsEmpty()
    {
        $this->assertEmpty($this->fixture->getLabel());
    }

    /**
     * @test
     */
    public function labelCanBeSet()
    {
        $value = 'label';

        $this->fixture->setLabel($value);
        $this->assertEquals($value, $this->fixture->getLabel());
    }

    /**
     * @test
     */
    public function defaultStaticIsFalse()
    {
        $this->assertFalse($this->fixture->isStatic());
    }

    /**
     * @test
     */
    public function staticCanBeSet()
    {
        $value = true;

        $this->fixture->setStatic($value);
        $this->assertEquals($value, $this->fixture->isStatic());
    }

    /**
     * @test
     */
    public function defaultPlaceholderIsEmpty()
    {
        $this->assertEmpty($this->fixture->getPlaceholder());
    }

    /**
     * @test
     */
    public function placeholderCanBeSet()
    {
        $value = 'placeholder';

        $this->fixture->setPlaceholder($value);
        $this->assertEquals($value, $this->fixture->getPlaceholder());
    }

    /**
     * @test
     */
    public function defaultTypeIsZero()
    {
        $this->assertEquals(0, $this->fixture->getType());
    }

    /**
     * @test
     */
    public function typeCanBeSet()
    {
        $value = 12;

        $this->fixture->setType($value);
        $this->assertEquals($value, $this->fixture->getType());
    }

    /**
     * @test
     */
    public function defaultValidationRulesIsEmpty()
    {
        $this->assertEmpty($this->fixture->getValidationRules());
    }

    /**
     * @test
     */
    public function validationRulesCanBeSet()
    {
        $value = 'required,test';

        $this->fixture->setValidationRules($value);
        $this->assertEquals($value, $this->fixture->getValidationRules());
    }

    /**
     * @test
     */
    public function getValidationRulesArrayReturnArrayOfRules()
    {
        $value = 'required,test';
        $expect = ['required', 'test'];

        $this->fixture->setValidationRules($value);
        $this->assertEquals($expect, $this->fixture->getValidationRulesArray());
    }

    /**
     * @test
     */
    public function defaultUserEmailFieldIsFalse()
    {
        $this->assertFalse($this->fixture->isUserEmailField());
    }

    /**
     * @test
     */
    public function userEmailFieldCanBeSet()
    {
        $value = true;

        $this->fixture->setUserEmailField($value);
        $this->assertEquals($value, $this->fixture->isUserEmailField());
    }

    /**
     * @test
     */
    public function defaultAdditionalTextIsEmpty()
    {
        $this->assertEmpty($this->fixture->getAdditionalText());
    }

    /**
     * @test
     */
    public function additionalTexCanBeSet()
    {
        $value = 'additionalTex';

        $this->fixture->setAdditionalText($value);
        $this->assertEquals($value, $this->fixture->getAdditionalText());
    }

    /**
     * @test
     */
    public function defaultValueIsEmpty()
    {
        $this->assertEmpty($this->fixture->getValue());
    }

    /**
     * @test
     */
    public function valueCanBeSet()
    {
        $value = 'value';

        $this->fixture->setValue($value);
        $this->assertEquals($value, $this->fixture->getValue());
    }

    /**
     * @test
     */
    public function defaultErrorsIsEmptyArray()
    {
        $this->assertEquals([], $this->fixture->getErrors());
    }

    /**
     * @test
     */
    public function errorsCanBeSet()
    {
        $value = ['error'];

        $this->fixture->setErrors($value);
        $this->assertEquals($value, $this->fixture->getErrors());
    }

    /**
     * @test
     */
    public function addErrorWillAddErrorToErrorsArray()
    {
        $value = ['error'];
        $expect = ['error', 'new error'];

        $this->fixture->setErrors($value);
        $this->fixture->addError('new error');


        $this->assertEquals($expect, $this->fixture->getErrors());
    }

    /**
     * @test
     */
    public function getValueAsTextByDefaultReturnSimpleString()
    {
        $value = 'string';

        $this->fixture->setValue($value);
        $this->fixture->setType(0);

        $this->assertEquals($value, $this->fixture->getValueAsText());
    }
}

<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model\OrderFormFields;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Option;
use Pixelant\PxaProductManager\Domain\Model\OrderFormField;
use Pixelant\PxaProductManager\Domain\Model\OrderFormFields\SelectBoxFormField;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class SelectBoxFormFieldTest
 * @package Pixelant\PxaProductManager\Tests\Unit\Domain\Model\OrderFormFields
 */
class SelectBoxFormFieldTest extends UnitTestCase
{
    /**
     * @var SelectBoxFormField
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new  SelectBoxFormField();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function getValueAsTextForSelectBoxReturnValueOfOption()
    {
        $expectValue = 'Nova poshta';
        $valueUid = 23;

        $option1 = new Option();
        $option1->_setProperty('uid', 12);
        $option1->setValue('test');

        $option2 = new Option();
        $option2->_setProperty('uid', $valueUid);
        $option2->setValue($expectValue);

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($option1);
        $objectStorage->attach($option2);

        $this->fixture->setOptions($objectStorage);
        $this->fixture->setValue($valueUid);
        $this->fixture->setType(OrderFormField::FIELD_SELECTBOX);

        $this->assertEquals($expectValue, $this->fixture->getValueAsText());
    }

    /**
     * @test
     */
    public function getOptionsReturnInitialObjectStorage()
    {
        $objectStorage = new ObjectStorage();

        $this->assertEquals($objectStorage, $this->fixture->getOptions());
    }

    /**
     * @test
     */
    public function setOptionsWillSetOptionsStorage()
    {
        $objectStorage = new ObjectStorage();

        $this->fixture->setOptions($objectStorage);

        $this->assertSame(
            $objectStorage,
            $this->fixture->getOptions()
        );
    }

    /**
     * @test
     */
    public function addOptionWillAddOptionToObjectStorage()
    {
        $option = new Option();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($option);

        $this->fixture->addOption($option);

        $this->assertEquals(
            $objectStorage,
            $this->fixture->getOptions()
        );
    }

    /**
     * @test
     */
    public function removeOptionFromObjectStorageRemoveItFromStorage()
    {
        $option = new Option();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($option);
        $objectStorage->detach($option);

        $this->fixture->addOption($option);
        $this->fixture->removeOption($option);

        $this->assertEquals(
            $objectStorage,
            $this->fixture->getOptions()
        );
    }
}

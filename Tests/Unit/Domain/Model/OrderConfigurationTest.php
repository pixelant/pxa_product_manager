<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\OrderConfiguration;
use Pixelant\PxaProductManager\Domain\Model\OrderFormField;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class OrderConfigurationTest extends UnitTestCase
{
    /**
     * @var OrderConfiguration
     */
    protected $fixture;

    public function setUp()
    {
        $this->fixture = new OrderConfiguration();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function defaultNameEmpty()
    {
        $this->assertEmpty($this->fixture->getName());
    }

    /**
     * @test
     */
    public function nameCanBeSet()
    {
        $value = 'Name';

        $this->fixture->setName($value);

        $this->assertEquals($value, $this->fixture->getName());
    }

    /**
     * @test
     */
    public function getFormFieldsReturnInitialObjectStorage()
    {
        $objectStorage = new ObjectStorage();

        $this->assertEquals($objectStorage, $this->fixture->getFormFields());
    }

    /**
     * @test
     */
    public function setFormFieldsWillSetFormFieldsStorage()
    {
        $objectStorage = new ObjectStorage();

        $this->fixture->setFormFields($objectStorage);

        $this->assertSame(
            $objectStorage,
            $this->fixture->getFormFields()
        );
    }

    /**
     * @test
     */
    public function addFormFieldWillAddFieldToObjectStorage()
    {
        $formField = new OrderFormField();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($formField);

        $this->fixture->addFormField($formField);

        $this->assertEquals(
            $objectStorage,
            $this->fixture->getFormFields()
        );
    }

    /**
     * @test
     */
    public function removeFormFieldFromObjectStorage()
    {
        $formField = new OrderFormField();
        $objectStorage = new ObjectStorage();
        $objectStorage->attach($formField);
        $objectStorage->detach($formField);

        $this->fixture->addFormField($formField);
        $this->fixture->removeFormField($formField);

        $this->assertEquals(
            $objectStorage,
            $this->fixture->getFormFields()
        );
    }

    /**
     * @test
     */
    public function defaultValueOfEnabledEmailToUser()
    {
        $this->assertFalse($this->fixture->isEnabledEmailToUser());
    }

    /**
     * @test
     */
    public function canSetEnabledEmailToUser()
    {
        $this->fixture->setEnabledEmailToUser(true);
        $this->assertTrue($this->fixture->isEnabledEmailToUser());
    }

    /**
     * @test
     */
    public function defaultValueOfEnabledReplaceWithFeUserFields()
    {
        $this->assertFalse($this->fixture->isEnabledReplaceWithFeUserFields());
    }

    /**
     * @test
     */
    public function canSetEnabledReplaceWithFeUserFields()
    {
        $this->fixture->setEnabledReplaceWithFeUserFields(true);
        $this->assertTrue($this->fixture->isEnabledReplaceWithFeUserFields());
    }

    /**
     * @test
     */
    public function defaultValueOfOrderFormFieldProcessed()
    {
        $this->assertFalse($this->fixture->_getProperty('orderFormFieldProcessed'));
    }

    /**
     * @test
     */
    public function defaultValueOfAdminEmails()
    {
        $this->assertEmpty($this->fixture->getAdminEmails());
    }

    /**
     * @test
     */
    public function canSetAdminEmails()
    {
        $admins = "andriy@pixelant.se\nmail@site.com";

        $this->fixture->setAdminEmails($admins);

        $this->assertEquals($admins, $this->fixture->getAdminEmails());
    }

    /**
     * @test
     */
    public function getAdminEmailsAsArrayReturnArrayOfTheseEmails()
    {
        $admins = "andriy@pixelant.se\nmail@site.com";
        $expect = ['andriy@pixelant.se', 'mail@site.com'];

        $this->fixture->setAdminEmails($admins);

        $this->assertEquals($expect, $this->fixture->getAdminEmailsArray());
    }

    /**
     * @test
     */
    public function prepareOrderFormFieldsWillReplaceValueWithFeUserFieldIfEnabled()
    {
        $userName = 'Fe user name';
        $user = new FrontendUser();
        $user->setName($userName);

        $ownValue = 'own value';
        $formField = new OrderFormField();
        $formField->setValue($ownValue);
        $formField->setName('name');

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($formField);

        $this->fixture->setEnabledReplaceWithFeUserFields(true);
        $this->fixture->_setProperty('frontendUser', $user);
        $this->fixture->setFormFields($objectStorage);

        $this->fixture->prepareOrderFormFields();

        $this->assertEquals($userName, $formField->getValue());
    }

    /**
     * @test
     */
    public function prepareOrderFormFieldsWillNotReplaceValueWithFeUserFieldIfDisabled()
    {
        $userName = 'Fe user name';
        $user = new FrontendUser();
        $user->setName($userName);

        $ownValue = 'own value';
        $formField = new OrderFormField();
        $formField->setValue($ownValue);
        $formField->setName('name');

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($formField);

        $this->fixture->setEnabledReplaceWithFeUserFields(false);
        $this->fixture->_setProperty('frontendUser', $user);
        $this->fixture->setFormFields($objectStorage);

        $this->fixture->prepareOrderFormFields();

        $this->assertEquals($ownValue, $formField->getValue());
    }

    /**
     * @test
     */
    public function getUserEmailFromFormFieldsFindFieldMarkedAsEmailAndReturnItValue()
    {
        $formField = new OrderFormField();
        $formField->setValue('Test name');
        $formField->setName('name');

        $mail = 'mail@site.com';
        $formField1 = new OrderFormField();
        $formField1->setValue($mail);
        $formField1->setName('email');
        $formField1->setUserEmailField(true);

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($formField);
        $objectStorage->attach($formField1);

        $this->fixture->setFormFields($objectStorage);

        $this->assertEquals($mail, $this->fixture->getUserEmailFromFormFields());
    }
}

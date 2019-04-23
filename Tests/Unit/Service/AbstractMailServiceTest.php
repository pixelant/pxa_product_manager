<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Service;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Exception\OrderEmailException;
use Pixelant\PxaProductManager\Service\Mail\AbstractMailService;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class AbstractMailServiceTest extends UnitTestCase
{
    /**
     * @var AbstractMailService
     */
    protected $subject = null;

    public function setUp()
    {
        $this->subject = $this->getAccessibleMockForAbstractClass(
            AbstractMailService::class,
            [],
            '',
            false,
            false,
            true,
            ['initPluginSettings']
        );
    }

    /**
     * @test
     */
    public function defaultPluginSettingsEmpty()
    {
        $this->assertEmpty(
            $this->subject->getPluginSettings()
        );
    }

    /**
     * @test
     */
    public function pluginSettingsCanBeSet()
    {
        $settings = ['test' => 1];

        $this->subject->setPluginSettings($settings);

        $this->assertEquals($settings, $this->subject->getPluginSettings());
    }

    /**
     * @test
     */
    public function defaultSenderNameEmpty()
    {
        $this->assertEmpty(
            $this->subject->getSenderName()
        );
    }

    /**
     * @test
     */
    public function senderNameCanBeSet()
    {
        $value = 'Name';

        $this->subject->setSenderName($value);

        $this->assertEquals($value, $this->subject->getSenderName());
    }

    /**
     * @test
     */
    public function defaultSenderEmailEmpty()
    {
        $this->assertEmpty(
            $this->subject->getSenderEmail()
        );
    }

    /**
     * @test
     */
    public function senderEmailCanBeSet()
    {
        $value = 'email';

        $this->subject->setSenderEmail($value);

        $this->assertEquals($value, $this->subject->getSenderEmail());
    }

    /**
     * @test
     */
    public function defaultReceiversEmpty()
    {
        $this->assertEmpty(
            $this->subject->getReceivers()
        );
    }

    /**
     * @test
     */
    public function receiversCanBeSet()
    {
        $receivers = ['noreply@site.com'];

        $this->subject->setReceivers($receivers);

        $this->assertEquals($receivers, $this->subject->getReceivers());
    }

    /**
     * @test
     */
    public function defaultSubjectEmpty()
    {
        $this->assertEmpty(
            $this->subject->getSubject()
        );
    }

    /**
     * @test
     */
    public function subjectCanBeSet()
    {
        $value = 'subject';

        $this->subject->setSubject($value);

        $this->assertEquals($value, $this->subject->getSubject());
    }

    /**
     * @test
     */
    public function defaultMessageEmpty()
    {
        $this->assertEmpty(
            $this->subject->getMessage()
        );
    }

    /**
     * @test
     */
    public function messageCanBeSet()
    {
        $value = 'message';

        $this->subject->setMessage($value);

        $this->assertEquals($value, $this->subject->getMessage());
    }

    /**
     * @test
     * @throws OrderEmailException
     */
    public function sendEmailWithoutSubjectThrowsException()
    {
        $this->subject->setSubject('');

        $this->expectException(OrderEmailException::class);
        $this->subject->send();
    }

    /**
     * @test
     * @throws OrderEmailException
     */
    public function sendEmailWithoutReceiversThrowsException()
    {
        $this->subject->setSubject('Test');
        $this->subject->setReceivers([]);

        $this->expectException(OrderEmailException::class);
        $this->subject->send();
    }

    /**
     * @test
     * @throws OrderEmailException
     */
    public function sendEmailWithoutSenderEmailThrowsException()
    {
        $this->subject->setSubject('Test');
        $this->subject->setReceivers(['andriy@site.com']);
        $this->subject->setSenderEmail('');

        $this->expectException(OrderEmailException::class);
        $this->subject->send();
    }

    /**
     * @test
     * @throws OrderEmailException
     */
    public function initViewWithoutTemplatePathAndNameThrowsException()
    {
        $mockedObjectManager = $this->createMock(ObjectManager::class);
        $this->subject->_set('objectManager', $mockedObjectManager);

        $this->expectException(OrderEmailException::class);
        $this->subject->_call('initializeStandaloneView', '', '');
    }

    public function tearDown()
    {
        unset($this->subject);
    }
}

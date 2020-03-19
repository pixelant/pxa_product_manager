<?php
namespace Pixelant\PxaProductManager\Tests\Unit\Configuration\Site;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Configuration\Site\SettingsReader;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @package Pixelant\PxaProductManager\Tests\Unit\Configuration\Flexform
 */
class SettingsReaderTest extends UnitTestCase
{

    /**
     * @test
     */
    public function initWillSetSettingsFromSiteConfiguration()
    {
        $settings = ['test' => 'test value'];

        $site = $this->prophesize(Site::class);
        $site->getConfiguration()->shouldBeCalled()->willReturn($settings);

        $request = $this->prophesize(ServerRequest::class);
        $request->getAttribute('site')->shouldBeCalled()->willReturn($site->reveal());

        $subject = new SettingsReader($request->reveal());
        $this->callInaccessibleMethod($subject, 'init');

        $this->assertEquals($settings, getProtectedVarValue($subject, 'settings'));
    }

    /**
     * @test
     */
    public function getValueReturnValueOfSettingAndAddPrefix()
    {
        $settings = [
            'pxapm_singleViewPid' => 11
        ];

        $subject = new SettingsReader($this->createMock(ServerRequest::class));
        $this->inject($subject, 'settings', $settings);

        $this->assertEquals(11, $subject->getValue('singleViewPid'));
    }


    /**
     * @test
     */
    public function getValueReturnValueIfGivenKeyIsWithPrefixAlready()
    {
        $settings = [
            'pxapm_singleViewPid' => 22
        ];

        $subject = new SettingsReader($this->createMock(ServerRequest::class));
        $this->inject($subject, 'settings', $settings);

        $this->assertEquals(22, $subject->getValue('pxapm_singleViewPid'));
    }
}

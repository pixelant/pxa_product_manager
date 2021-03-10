<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Configuration\Site;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Configuration\Site\SettingsReader;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

class SettingsReaderTest extends UnitTestCase
{
    /**
     * @test
     */
    public function initWillSetSettingsFromSiteConfiguration(): void
    {
        $settings = ['test' => 'test value'];

        $site = $this->prophesize(Site::class);
        $site->getConfiguration()->shouldBeCalled()->willReturn($settings);

        $request = $this->prophesize(ServerRequest::class);
        $request->getAttribute('site')->shouldBeCalled()->willReturn($site->reveal());

        $subject = new SettingsReader();
        $subject->injectServerRequest($request->reveal());
        $this->callInaccessibleMethod($subject, 'init');

        self::assertEquals($settings, TestsUtility::getProtectedVarValue($subject, 'settings'));
    }

    /**
     * @test
     */
    public function getValueReturnValueOfSettingAndAddPrefix(): void
    {
        $settings = [
            'pxapm_singleViewPid' => 11,
        ];

        $subject = new SettingsReader($this->createMock(ServerRequest::class));
        $this->inject($subject, 'settings', $settings);

        self::assertEquals(11, $subject->getValue('singleViewPid'));
    }

    /**
     * @test
     */
    public function getValueReturnValueIfGivenKeyIsWithPrefixAlready(): void
    {
        $settings = [
            'pxapm_singleViewPid' => 22,
        ];

        $subject = new SettingsReader($this->createMock(ServerRequest::class));
        $this->inject($subject, 'settings', $settings);

        self::assertEquals(22, $subject->getValue('pxapm_singleViewPid'));
    }
}

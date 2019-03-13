<?php

namespace Pixelant\PxaProductManager\Tests\Unit\ViewHelpers;

use Nimut\TestingFramework\TestCase\ViewHelperBaseTestcase;
use Pixelant\PxaProductManager\ViewHelpers\SetPageTitleViewHelper;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class SetPageTitleViewHelperTest
 * @package Pixelant\PxaProductManager\Tests\ViewHelpers
 */
class SetPageTitleViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SetPageTitleViewHelper
     */
    protected $viewHelper;

    protected function setUp()
    {
        parent::setUp();
        $tsfe = $this->getMockBuilder(TypoScriptFrontendController::class)
                        ->disableOriginalConstructor()
                        ->disableOriginalClone()
                        ->getMock();
        $this->viewHelper = $this->getAccessibleMock(SetPageTitleViewHelper::class, ['buildRenderChildrenClosure']);
        $this->inject($this->viewHelper, 'renderingContext', $this->createMock(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface::class));
        $this->viewHelper->initializeArguments();

        $GLOBALS['TSFE'] = $tsfe;

        defined('LF') ?: define('LF', chr(10));
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->viewHelper, $GLOBALS['TSFE']);
    }

    /**
     * @test
     */
    public function titleTagIsSet()
    {
        $title = 'Custom title of page';

        $this->viewHelper->_set('arguments', ['title' => $title]);
        $this->viewHelper
            ->expects($this->atLeastOnce())
            ->method('buildRenderChildrenClosure')
            ->willReturn(function () {
                return 'test';
            });
        $this->viewHelper->render();

        $this->assertEquals($title, $GLOBALS['TSFE']->altPageTitle);
        $this->assertEquals($title, $GLOBALS['TSFE']->indexedDocTitle);
        $this->assertEquals($title, GeneralUtility::makeInstance(PageRenderer::class)->getTitle());
    }

    /**
     * @test
     */
    public function titleTagNoTrimmedStringIsSetAsTrimmedString()
    {
        $title = '  Custom title of page  ';

        $this->viewHelper->_set('arguments', ['title' => $title]);
        $this->viewHelper
            ->expects($this->atLeastOnce())
            ->method('buildRenderChildrenClosure')
            ->willReturn(function () {
                return 'test';
            });
        $this->viewHelper->render();

        $expect = trim($title);
        self::assertEquals($expect, $GLOBALS['TSFE']->altPageTitle);
        self::assertEquals($expect, $GLOBALS['TSFE']->indexedDocTitle);
        $this->assertEquals($expect, GeneralUtility::makeInstance(PageRenderer::class)->getTitle());
    }
}

<?php

namespace Pixelant\PxaProductManager\Tests\Unit\ViewHelpers;

use Nimut\TestingFramework\TestCase\ViewHelperBaseTestcase;
use Pixelant\PxaProductManager\ViewHelpers\SetPageTitleViewHelper;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class SetPageTitleViewHelperTest
 * @package Pixelant\PxaProductManager\Tests\ViewHelpers
 */
class SetPageTitleViewHelperTest extends ViewHelperBaseTestcase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected $tsfe;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|SetPageTitleViewHelper
     */
    protected $viewHelper;

    protected function setUp()
    {
        parent::setUp();
        $this->tsfe = $this->getMockBuilder(TypoScriptFrontendController::class)
                        ->disableOriginalConstructor()
                        ->disableOriginalClone()
                        ->getMock();
        $this->viewHelper = $this->getAccessibleMock(SetPageTitleViewHelper::class, ['dummy']);
        $this->viewHelper->initializeArguments();

        $GLOBALS['TSFE'] = $this->tsfe;
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->viewHelper, $this->tsfe, $GLOBALS['TSFE']);
    }

    /**
     * @test
     */
    public function titleTagIsSet()
    {
        $title = 'Custom title of page';

        $this->viewHelper->_set('arguments', ['title' => $title]);

        $this->viewHelper->render();

        self::assertEquals($title, $GLOBALS['TSFE']->altPageTitle);
        self::assertEquals($title, $GLOBALS['TSFE']->indexedDocTitle);
    }

    /**
     * @test
     */
    public function titleTagNoTrimmedStringIsSetAsTrimmedString()
    {
        $title = '  Custom title of page  ';

        $this->viewHelper->_set('arguments', ['title' => $title]);

        $this->viewHelper->render();

        self::assertEquals(trim($title), $GLOBALS['TSFE']->altPageTitle);
        self::assertEquals(trim($title), $GLOBALS['TSFE']->indexedDocTitle);
    }
}

<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Service\Url;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderService;
use Pixelant\PxaProductManager\Tests\Utility\TestsUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class UrlBuilderServiceTest extends UnitTestCase
{
    /**
     * @var UrlBuilderService
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        // @codingStandardsIgnoreStart
        $GLOBALS['TSFE'] = new class($this->createMock(ContentObjectRenderer::class)) extends TypoScriptFrontendController {
            /** @codingStandardsIgnoreEnd */
            public $cObj;

            /**
             * @param $cObj
             */
            public function __construct($cObj)
            {
                $this->cObj = $cObj;
            }
        };

        $this->subject = new UrlBuilderService();
    }

    /**
     * @test
     */
    public function canSetAbsoluteUrl(): void
    {
        $this->subject->absolute(true);

        self::assertTrue(TestsUtility::getProtectedVarValue($this->subject, 'absolute'));
    }
}

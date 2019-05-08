<?php

namespace Pixelant\PxaProductManager\Tests\Unit\Service\Link;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Service\Link\LinkBuilderService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class LinkBuilderServiceTest
 * @package Pixelant\PxaProductManager\Tests\Unit\Service\Link
 */
class LinkBuilderServiceTest extends UnitTestCase
{
    /**
     * @var LinkBuilderService
     */
    protected $subject = null;

    public function setUp()
    {
        $this->subject = $this->getAccessibleMock(
            LinkBuilderService::class,
            null,
            [],
            '',
            false,
            false
        );
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function defaultLanguageUidIsZero()
    {
        $this->assertEquals(0, $this->subject->_get('languageUid'));
    }

    /**
     * @test
     */
    public function languagePassedToConstructorIsSetAsDefault()
    {
        $languageUid = 12;

        $subject = $this->getAccessibleMock(
            LinkBuilderService::class,
            null,
            [$languageUid]
        );

        $this->assertEquals($languageUid, $subject->_get('languageUid'));
    }

    /**
     * @test
     */
    public function defaultTypoScriptFrontendControllerIsNull()
    {
        $this->assertNull($this->subject->_get('typoScriptFrontendController'));
    }

    /**
     * @test
     */
    public function typoScriptFrontendControllerPassedToConstructorIsSet()
    {
        $tsfe = $this->createMock(TypoScriptFrontendController::class);

        $subject = $this->getAccessibleMock(
            LinkBuilderService::class,
            null,
            [null, $tsfe]
        );

        $this->assertSame($tsfe, $subject->_get('typoScriptFrontendController'));
    }

    /**
     * @test
     */
    public function setLanguageUidWillSetLanguageUid()
    {
        $languageUid = 22;

        $this->subject->setLanguageUid($languageUid);

        $this->assertEquals($languageUid, $this->subject->_get('languageUid'));
    }

    /**
     * @test
     */
    public function getProductCategoryUidReturnUidOfCategoryObject()
    {
        $uid = 233;
        $productUid = 123;

        $category = new Category();
        $category->_setProperty('uid', $uid);

        $this->assertEquals($uid, $this->subject->_call('getProductCategoryUid', $productUid, $category));
    }

    /**
     * @test
     */
    public function getProductCategoryUidReturnUidOfCategoryGiven()
    {
        $categoryUid = 500;
        $productUid = 123;

        $this->assertEquals($categoryUid, $this->subject->_call('getProductCategoryUid', $productUid, $categoryUid));
    }

    /**
     * @test
     */
    public function getProductCategoryUidReturnUidOfProductFirstCategory()
    {
        $uid = 455;

        $category = new Category();
        $category->_setProperty('uid', $uid);

        $product = new Product();
        $product->addCategory($category);

        $this->assertEquals($uid, $this->subject->_call('getProductCategoryUid', $product, $category));
    }
}

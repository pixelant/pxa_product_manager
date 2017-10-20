<?php

namespace Pixelant\PxaProductManager\Tests\Functional\Navigation;

use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Controller\NavigationController;
use Pixelant\PxaProductManager\Navigation\BreadcrumbsBuilder;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class BreadcrumbsBuilderTest
 * @package Pixelant\PxaProductManager\Tests\Functional\Navigation
 */
class BreadcrumbsBuilderTest extends UnitTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AccessibleMockObjectInterface|BreadcrumbsBuilder
     */
    protected $mockedBreadcrumbsBuilder;

    protected function setUp()
    {
        $this->mockedBreadcrumbsBuilder = $this->getAccessibleMock(
            BreadcrumbsBuilder::class,
            ['dummy'],
            [],
            '',
            false
        );

        $this->mockedBreadcrumbsBuilder->cObj = $this->createPartialMock(
            ContentObjectRenderer::class,
            ['getTypoLink_URL']
        );

        $tsfe = $this->getAccessibleMock(
            TypoScriptFrontendController::class,
            [],
            [],
            '',
            false,
            false
        );

        $tsfe->id = 123;

        $GLOBALS['TSFE'] = $tsfe;
    }

    /**
     * @test
     */
    public function buildingBreadCrumbsForCategoriesWillReturnLinkForChainOfCategories()
    {
        list($category1, $category2, $category3, $category4) = [12, 15, 20, 23];

        $currentCategory = 321;

        $breadCrumbsCategories = [
            ['uid' => $category1],
            ['uid' => $category2],
            ['uid' => $category3],
            ['uid' => $category4],
        ];

        $linkParams = [
            NavigationController::CATEGORY_ARG_START_WITH . '0' => $category1,
            NavigationController::CATEGORY_ARG_START_WITH . '1' => $category2,
            NavigationController::CATEGORY_ARG_START_WITH . '2' => $category3,
            NavigationController::CATEGORY_ARG_START_WITH . '3' => $category4,
            NavigationController::CATEGORY_ARG_START_WITH . '4' => $currentCategory
        ];
        $expectParameters = [
            'tx_pxaproductmanager_pi1' => $linkParams
        ];

        $this->mockedBreadcrumbsBuilder->cObj
            ->expects($this->once())
            ->method('getTypoLink_URL')
            ->with(
                123, // page uid
                $expectParameters
            )
            ->willReturn('');

        $this->mockedBreadcrumbsBuilder->_call('buildLink', $breadCrumbsCategories, $currentCategory);
    }

    /**
     * @test
     */
    public function buildingBreadCrumbsForCategoriesAndProductWillReturnLinkForChainOfCategoriesAndProduct()
    {
        list($category1, $category2, $category3, $category4) = [12, 15, 20, 23];

        $product = 321;

        $breadCrumbsCategories = [
            ['uid' => $category1],
            ['uid' => $category2],
            ['uid' => $category3],
            ['uid' => $category4],
        ];

        $linkParams = [
            NavigationController::CATEGORY_ARG_START_WITH . '0' => $category1,
            NavigationController::CATEGORY_ARG_START_WITH . '1' => $category2,
            NavigationController::CATEGORY_ARG_START_WITH . '2' => $category3,
            NavigationController::CATEGORY_ARG_START_WITH . '3' => $category4,
            'product' => $product
        ];
        $expectParameters = [
            'tx_pxaproductmanager_pi1' => $linkParams
        ];

        $this->mockedBreadcrumbsBuilder->cObj
            ->expects($this->once())
            ->method('getTypoLink_URL')
            ->with(
                123, // page uid
                $expectParameters
            )
            ->willReturn('');

        $this->mockedBreadcrumbsBuilder->_call('buildLink', $breadCrumbsCategories, $product, true);
    }
}

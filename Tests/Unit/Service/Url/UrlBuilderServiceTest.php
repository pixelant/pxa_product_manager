<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Tests\Unit\Service\Url;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Service\Url\UrlBuilderService;
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

        $GLOBALS['TSFE'] = new class($this->createMock(ContentObjectRenderer::class)) extends TypoScriptFrontendController {
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

        self::assertTrue(getProtectedVarValue($this->subject, 'absolute'));
    }

    /**
     * @test
     */
    public function getCategoriesArgumentsGenerateEmptyArgumentsFromEmptyNavigationTree(): void
    {
        $rootCategory = createEntity(Category::class, 10);
        $rootCategory->setHiddenInNavigation(true);

        // expect empty array with one hidden root category
        $expect = [];

        self::assertEquals($expect, $this->callInaccessibleMethod($this->subject, 'getCategoriesArguments', $rootCategory));
    }

    /**
     * @test
     */
    public function getCategoriesArgumentsGenerateArgumentsFromRootLine(): void
    {
        $lastCategory = createCategoriesRootLineAndReturnLastCategory();
        $lastCategory->setHiddenInNavigation(true);

        $testCategory = createEntity(Category::class, 100);
        $testCategory->setParent($lastCategory);

        $prefix = UrlBuilderService::CATEGORY_ARGUMENT_START_WITH;
        // Current subject as last,
        // exclude last category from given rootline
        $expect = [
            $prefix . '0' => 1,
            $prefix . '1' => 2,
            $prefix . '2' => 3,
            $prefix . '3' => 4,
            'category' => $testCategory->getUid(),
        ];

        self::assertEquals($expect, $this->callInaccessibleMethod($this->subject, 'getCategoriesArguments', $testCategory));
    }

    /**
     * @test
     */
    public function createParamsAddCategoriesToParams(): void
    {
        $subject = $this->createPartialMock(UrlBuilderService::class, ['getCategoriesArguments']);
        $category = createEntity(Category::class, 1);

        $subject->expects(self::once())->method('getCategoriesArguments')->with($category)->willReturn(['category' => 1]);

        $params = $this->callInaccessibleMethod($subject, 'createParams', $category, null);
        $expect = [
            'controller' => 'Product',
            'action' => 'list',
            'category' => 1,
        ];
        self::assertEquals($expect, $params);
    }

    /**
     * @test
     */
    public function createParamsAddProductToParamsAndChangeAction(): void
    {
        $product = createEntity(Product::class, 10);

        $params = $this->callInaccessibleMethod($this->subject, 'createParams', null, $product);
        $expect = [
            'controller' => 'Product',
            'action' => 'show',
            'product' => 10,
        ];
        self::assertEquals($expect, $params);
    }
}

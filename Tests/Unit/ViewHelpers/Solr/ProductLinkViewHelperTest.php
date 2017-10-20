<?php

namespace Pixelant\PxaProductManager\Tests\Unit\ViewHelpers\Solr;

use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\ViewHelpers\Solr\ProductLinkViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class ProductLinkViewHelperTest
 * @package Pixelant\PxaProductManager\Tests\Unit\ViewHelpers\Solr
 */
class ProductLinkViewHelperTest extends UnitTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AccessibleMockObjectInterface|ProductLinkViewHelper
     */
    protected $viewHelper;

    protected function setUp()
    {
        $this->viewHelper = $this->getAccessibleMock(
            ProductLinkViewHelper::class,
            ['dummy'],
            [],
            '',
            false
        );

        $settings['solr']['productManagerTypes'] = 'product_search_type';

        $this->viewHelper->_set('settings', $settings);

        $tsfe = $this->createMock(TypoScriptFrontendController::class);
        $tsfe->cObj = $this->createPartialMock(ContentObjectRenderer::class, ['getTypoLink_URL']);

        $GLOBALS['TSFE'] = $tsfe;
    }

    /**
     * @test
     */
    public function generateLinkForProductWhenTypeIsAllowed()
    {
        $document = [
            'uid' => 1,
            'type' => 'product_search_type'
        ];

        $product = new Product();
        $product->_setProperty('uid', 1);

        $productRepository = $this->createPartialMock(ProductRepository::class, ['findByUid']);
        $productRepository
            ->expects($this->once())
            ->method('findByUid')
            ->with(1)
            ->willReturn($product);

        $this->viewHelper->_set('productRepository', $productRepository);

        $GLOBALS['TSFE']->cObj
            ->expects($this->once())
            ->method('getTypoLink_URL')
            ->willReturn('');

        $this->viewHelper->execute([serialize($document)]);
    }

    /**
     * @test
     */
    public function generateLinkWithInvalidTypeWillReturnEmptyString()
    {
        $document = [
            'uid' => 1,
            'type' => 'invalid'
        ];

        $this->assertEmpty($this->viewHelper->execute([serialize($document)]));
    }

    /**
     * @test
     */
    public function generateLinkWhenProductIsNotValidWillReturnEmptyString()
    {
        $document = [
            'uid' => 1,
            'type' => 'product_search_type'
        ];

        $productRepository = $this->createPartialMock(ProductRepository::class, ['findByUid']);
        $productRepository
            ->expects($this->once())
            ->method('findByUid')
            ->with(1)
            ->willReturn(null);

        $this->viewHelper->_set('productRepository', $productRepository);

        $GLOBALS['TSFE']->cObj
            ->expects($this->never())
            ->method('getTypoLink_URL');

        $this->assertEmpty($this->viewHelper->execute([serialize($document)]));
    }

    protected function tearDown()
    {
        unset($this->viewHelper, $GLOBALS['TSFE']);
    }
}
